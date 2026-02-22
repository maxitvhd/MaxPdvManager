import sys
import os
import fitz  # PyMuPDF
from weasyprint import HTML, CSS

# Target: 1080x1920px
# WeasyPrint at 96dpi: 1pt = 1.333px, so:
# 1080px / 1.333 = 810pt width
# 1920px / 1.333 = 1440pt height
# PyMuPDF renders at 72dpi by default, so zoom needed: 96/72 = 1.3333
# At zoom 1.3333: 810pt * 1.3333 = 1080px exactly

PAGE_W_PT = 810    # 1080px at 96dpi
PAGE_H_PT = 1440   # 1920px at 96dpi
ZOOM = 96 / 72     # ~1.3333 — renders PyMuPDF output at 96dpi (screen resolution)


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
            # ─── Step 1: WeasyPrint renders HTML to PDF ───────────────────────
            # Force EXACT 1080x1920 page via @page in CSS
            # The theme CSS inside the HTML already sets body height:1920px
            # but we also enforce @page to guarantee the PDF page size
            custom_css = CSS(string=f'''
                @page {{
                    size: {PAGE_W_PT}pt {PAGE_H_PT}pt;
                    margin: 0;
                }}
                html {{
                    margin: 0;
                    padding: 0;
                }}
            ''')

            import tempfile
            with tempfile.NamedTemporaryFile(suffix='.pdf', delete=False) as tmp:
                tmp_path = tmp.name

            HTML(filename=html_path, base_url=base_dir).write_pdf(
                tmp_path, stylesheets=[custom_css]
            )

            # ─── Step 2: PyMuPDF converts PDF page 0 to PNG at 96dpi ──────────
            doc = fitz.open(tmp_path)
            page = doc.load_page(0)

            mat = fitz.Matrix(ZOOM, ZOOM)
            # Clip exactly to expected page (prevents overshooting due to rounding)
            clip = fitz.Rect(0, 0, PAGE_W_PT, PAGE_H_PT)
            pix = page.get_pixmap(matrix=mat, clip=clip, alpha=False)

            doc.close()
            os.remove(tmp_path)

            pix.save(output_path)
            print(f"SUCESSO: {output_path} ({pix.width}x{pix.height}px)")

        else:
            # PDF mode: standard A4
            custom_css = CSS(string='@page { margin: 10mm; }')
            HTML(filename=html_path, base_url=base_dir).write_pdf(
                output_path, stylesheets=[custom_css]
            )
            print(f"SUCESSO: {output_path}")

    except Exception as e:
        print(f"ERRO_WEASYPRINT: {e}")
        import traceback
        traceback.print_exc()
        sys.exit(1)


if __name__ == "__main__":
    main()
