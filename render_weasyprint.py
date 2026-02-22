import sys
import os
import fitz  # PyMuPDF — importado no topo, escopo global
from weasyprint import HTML, CSS

# TARGET: Always output exactly 1080x1920 pixels for Instagram/Story format
TARGET_W = 1080
TARGET_H = 1920


def render_html_to_pixmap(html_path, base_dir, page_width_pt=810):
    """Renders HTML to a single high-res fitz Pixmap via WeasyPrint PDF."""
    pdf_bytes_buffer = []

    custom_css = CSS(string=f'''
        @page {{
            size: {page_width_pt}pt;
            margin: 0;
        }}
        html, body {{
            margin: 0;
            padding: 0;
            width: {page_width_pt}pt;
            background-color: white;
        }}
    ''')

    # Write PDF to a temp file (WeasyPrint only supports file output)
    import tempfile
    with tempfile.NamedTemporaryFile(suffix='.pdf', delete=False) as tmp:
        tmp_path = tmp.name

    HTML(filename=html_path, base_url=base_dir).write_pdf(tmp_path, stylesheets=[custom_css])

    # Open with fitz and render all pages at 2x zoom for high quality
    doc = fitz.open(tmp_path)
    zoom = 2.0
    mat = fitz.Matrix(zoom, zoom)

    all_pixmaps = []
    total_height = 0
    render_width = 0

    for page_num in range(len(doc)):
        page = doc.load_page(page_num)
        pix = page.get_pixmap(matrix=mat, alpha=False)
        all_pixmaps.append(pix)
        total_height += pix.height
        render_width = max(render_width, pix.width)

    doc.close()
    os.remove(tmp_path)

    if len(all_pixmaps) == 1:
        return all_pixmaps[0]

    # Stitch multiple pages vertically into one Pixmap
    combined = fitz.Pixmap(fitz.csRGB, fitz.IRect(0, 0, render_width, total_height), False)
    combined.set_rect(combined.irect, (255, 255, 255))
    y_offset = 0
    for pix in all_pixmaps:
        # Copy pixels manually by saving and reopening (safest cross-version approach)
        img_bytes = pix.tobytes("png")
        sub_doc = fitz.open("png", img_bytes)
        sub_page = sub_doc.load_page(0)
        sub_pix = sub_page.get_pixmap(alpha=False)
        combined.copy(sub_pix, fitz.IRect(0, y_offset, sub_pix.width, y_offset + sub_pix.height))
        y_offset += pix.height
        sub_doc.close()

    return combined


def resize_to_target(pix):
    """Uses Pillow to resize/pad the pixmap to exactly TARGET_W x TARGET_H."""
    try:
        from PIL import Image
        import io

        img_bytes = pix.tobytes("png")
        img = Image.open(io.BytesIO(img_bytes))
        src_w, src_h = img.size

        # Scale to correct width
        scale = TARGET_W / src_w
        new_w = TARGET_W
        new_h = int(src_h * scale)
        img_resized = img.resize((new_w, new_h), Image.LANCZOS)

        if new_h <= TARGET_H:
            # Content shorter: pad with background color at bottom
            bg_color = img_resized.getpixel((0, 0))
            if isinstance(bg_color, int):
                bg_color = (bg_color, bg_color, bg_color)
            final_img = Image.new("RGB", (TARGET_W, TARGET_H), bg_color[:3])
            final_img.paste(img_resized, (0, 0))
        else:
            # Content taller: scale down to fit ALL content within TARGET_H
            scale_down = TARGET_H / new_h
            fit_w = int(new_w * scale_down)
            img_fitted = img_resized.resize((fit_w, TARGET_H), Image.LANCZOS)
            bg_color = img_fitted.getpixel((0, 0))
            if isinstance(bg_color, int):
                bg_color = (bg_color, bg_color, bg_color)
            final_img = Image.new("RGB", (TARGET_W, TARGET_H), bg_color[:3])
            x_offset = (TARGET_W - fit_w) // 2
            final_img.paste(img_fitted, (x_offset, 0))

        return final_img, True

    except ImportError:
        return pix, False  # Pillow not available, return raw pixmap


def main():
    if len(sys.argv) < 4:
        print("Uso: python render_weasyprint.py <html_path> <output_path> <format: image|pdf>")
        sys.exit(1)

    html_path = sys.argv[1]
    output_path = sys.argv[2]
    fmt = sys.argv[3]

    if not os.path.exists(html_path):
        print(f"Erro: HTML nao encontrado em {html_path}")
        sys.exit(1)

    try:
        base_dir = os.path.dirname(os.path.abspath(html_path))

        if fmt == 'image':
            # WeasyPrint uses 96dpi: 1080px = 810pt
            pix = render_html_to_pixmap(html_path, base_dir, page_width_pt=810)

            result, used_pillow = resize_to_target(pix)

            if used_pillow:
                result.save(output_path, "PNG", optimize=True)
                print(f"SUCESSO: {output_path} ({TARGET_W}x{TARGET_H} via Pillow)")
            else:
                # Fallback: save raw pixmap without resizing
                pix.save(output_path)
                print(f"SUCESSO (sem Pillow — tamanho nativo): {output_path}")

        else:
            # PDF mode: standard A4 with WeasyPrint
            custom_css = CSS(string='@page { margin: 10mm; }')
            HTML(filename=html_path, base_url=base_dir).write_pdf(output_path, stylesheets=[custom_css])
            print(f"SUCESSO: {output_path}")

    except Exception as e:
        print(f"ERRO_WEASYPRINT: {e}")
        import traceback
        traceback.print_exc()
        sys.exit(1)


if __name__ == "__main__":
    main()
