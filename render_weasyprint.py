import sys
import os
import fitz  # PyMuPDF
from weasyprint import HTML, CSS

# TARGET: Always output exactly 1080x1920 pixels for Instagram/Story format
TARGET_W = 1080
TARGET_H = 1920

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
        pdf_temp = output_path + ".temp.pdf" if fmt == "image" else output_path

        if fmt == 'image':
            # Step 1: Render HTML to PDF with 1080px width and let height flow naturally
            # WeasyPrint at 96dpi: 1080px = 810pt
            page_width_pt = 810  # 1080px at 96dpi converted to points
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
            HTML(filename=html_path, base_url=base_dir).write_pdf(pdf_temp, stylesheets=[custom_css])

            # Step 2: Convert PDF to high-res PNG using PyMuPDF
            doc = fitz.open(pdf_temp)

            # Process all pages (in case content overflows to multiple pages)
            all_pixmaps = []
            total_height = 0
            render_width = 0

            # Render at 2x zoom for high quality (1080px * 2 = 2160px wide)
            zoom = 2.0
            mat = fitz.Matrix(zoom, zoom)

            for page_num in range(len(doc)):
                page = doc.load_page(page_num)
                pix = page.get_pixmap(matrix=mat, alpha=False)
                all_pixmaps.append(pix)
                total_height += pix.height
                render_width = max(render_width, pix.width)

            doc.close()

            # Step 3: Stitch all pages together if multi-page (rare but safe)
            if len(all_pixmaps) == 1:
                combined_pix = all_pixmaps[0]
            else:
                import fitz
                # Create a combined image
                combined = fitz.Pixmap(fitz.csRGB, (0, 0, render_width, total_height), False)
                combined.clear_with(255)  # white background
                y_offset = 0
                for pix in all_pixmaps:
                    combined.copy(pix, fitz.IRect(0, y_offset, pix.width, y_offset + pix.height))
                    y_offset += pix.height
                combined_pix = combined

            # Step 4: Use Pillow to resize/crop to exactly TARGET_W x TARGET_H
            # This ensures the output is always 1080x1920 without cutting content:
            # - Scale to fill 1080px width
            # - If content is shorter than 1920px: pad with background color at bottom
            # - If content is taller than 1920px: scale down to fit entirely within 1920px
            try:
                from PIL import Image
                import io

                img_bytes = combined_pix.tobytes("png")
                img = Image.open(io.BytesIO(img_bytes))
                src_w, src_h = img.size

                # Calculate scale to fit width exactly at 1080
                scale = TARGET_W / src_w
                new_w = TARGET_W
                new_h = int(src_h * scale)

                # Resize to correct width
                img_resized = img.resize((new_w, new_h), Image.LANCZOS)

                if new_h <= TARGET_H:
                    # Content shorter than 1920px: add bottom padding with background color
                    # Detect background color from top-left corner
                    bg_color = img_resized.getpixel((0, 0))
                    if isinstance(bg_color, int):
                        bg_color = (bg_color, bg_color, bg_color)
                    final_img = Image.new("RGB", (TARGET_W, TARGET_H), bg_color[:3])
                    final_img.paste(img_resized, (0, 0))
                else:
                    # Content taller than 1920px: scale down to fit ALL content within 1920px
                    scale_down = TARGET_H / new_h
                    fit_w = int(new_w * scale_down)
                    fit_h = TARGET_H
                    img_fitted = img_resized.resize((fit_w, fit_h), Image.LANCZOS)
                    # Detect background color
                    bg_color = img_fitted.getpixel((0, 0))
                    if isinstance(bg_color, int):
                        bg_color = (bg_color, bg_color, bg_color)
                    final_img = Image.new("RGB", (TARGET_W, TARGET_H), bg_color[:3])
                    x_offset = (TARGET_W - fit_w) // 2
                    final_img.paste(img_fitted, (x_offset, 0))

                final_img.save(output_path, "PNG", optimize=True)
                print(f"SUCESSO: {output_path} ({TARGET_W}x{TARGET_H})")

            except ImportError:
                # Pillow not available, save raw (may not be exactly 1080x1920)
                combined_pix.save(output_path)
                print(f"SUCESSO (sem Pillow): {output_path}")

        else:
            # PDF mode: standard A4 with WeasyPrint
            custom_css = CSS(string='@page { margin: 10mm; }')
            HTML(filename=html_path, base_url=base_dir).write_pdf(pdf_temp, stylesheets=[custom_css])
            print(f"SUCESSO: {output_path}")

        # Cleanup temp PDF
        if fmt == "image" and os.path.exists(pdf_temp):
            os.remove(pdf_temp)

    except Exception as e:
        print(f"ERRO_WEASYPRINT: {e}")
        import traceback
        traceback.print_exc()
        sys.exit(1)

if __name__ == "__main__":
    main()
