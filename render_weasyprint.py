import sys
import os
import fitz  # PyMuPDF
from weasyprint import HTML

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
        # Sempre gera o PDF com WeasyPrint primeiro
        pdf_temp = output_path + ".temp.pdf" if fmt == "image" else output_path
        
        # O Base URL pega o diretorio do HTML para carregar imagens relativas corretamente
        base_dir = os.path.dirname(os.path.abspath(html_path))
        
        # Renderiza o PDF
        HTML(filename=html_path, base_url=base_dir).write_pdf(pdf_temp)

        if fmt == "image":
            # Converte a primeira pagina do PDF para PNG usando PyMuPDF
            doc = fitz.open(pdf_temp)
            page = doc.load_page(0)
            
            # Aumenta a resolucao (scale) - Fator 2x equivale a alta resolucao
            zoom = 2.0
            mat = fitz.Matrix(zoom, zoom)
            pix = page.get_pixmap(matrix=mat)
            
            pix.save(output_path)
            doc.close()
            
            # Remove o PDF temporario
            if os.path.exists(pdf_temp):
                os.remove(pdf_temp)

        print(f"SUCESSO: {output_path}")

    except Exception as e:
        print(f"ERRO_WEASYPRINT: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()
