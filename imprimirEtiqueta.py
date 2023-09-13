from reportlab.lib.pagesizes import landscape
from reportlab.lib.units import cm
from reportlab.pdfgen import canvas
import qrcode
import tempfile
import os
from reportlab.graphics import barcode
from PIL import Image
import cups #Importe o pacote pycups

def criar_pdf(saida_pdf, titulo, cliente, pedido, transportadora):
    # Configurações das etiquetas e colunas
    label_width = 10 * cm
    label_height = 2.5 * cm
    column_gap = 0.5 * cm
    num_columns = 3
    num_rows = 3

    # Configurações do QR code
    qr_code_width = label_width
    qr_code_height = label_height * num_rows
    qr_code_padding = 0.5 * cm

    # Configurações do código de barras
    barcode_height = label_height
    barcode_padding = 0.5 * cm
    barcode_bar_width = 0.03 * cm  # Largura das barras verticais do código de barras

    # Criar o PDF e ajustar o tamanho da página para paisagem com tamanho personalizado
    custom_page_size = landscape((label_width*num_columns + column_gap*(num_columns-1),
                                  label_height*num_rows))

    # Criar um arquivo temporário para salvar o QR code
    with tempfile.NamedTemporaryFile(delete=False, suffix=".png") as temp_qr_file:
        qr_filename = temp_qr_file.name

        c = canvas.Canvas(saida_pdf, pagesize=custom_page_size)

        # Posição inicial da primeira etiqueta
        x, y = 0, label_height*num_rows

        # Título centralizado
        c.setFont("Helvetica-Bold", 20)
        title = titulo
        title_width = c.stringWidth(title, "Helvetica-Bold", 10)
        c.drawString(title_width * 2, 180, title)

        qr = qrcode.QRCode(version=1, box_size=int(qr_code_width / 30), border=0)
        qr.add_data(cliente)  # Substitua pelo link desejado
        qr.make(fit=True)
        qr_img = qr.make_image(fill_color="black", back_color="white")
        qr_img.save(qr_filename)  # Salvar a imagem do QR code no arquivo temporário
        c.drawImage(qr_filename, label_width * 2.15, 10, width=qr_code_width - 2 * qr_code_padding, height=qr_code_height - 2 * qr_code_padding)

        barcode_value = cliente  # Substitua pelo valor do código de barras desejado
        barcode_code128 = barcode.code128.Code128(barcode_value, barHeight=barcode_height - 2 * barcode_padding, humanReadable=True, barWidth=barcode_bar_width)
        # Desenhar o código de barras diretamente no canvas
        barcode_code128.drawOn(c, label_width * 1.2, 20)

        c.setFont("Helvetica", 35)
        c.drawString(10, 130, "Nº Cliente:")
        c.drawString(10, 80, "Nº Pedido:")
        c.drawString(10, 30, transportadora)

        c.drawString(label_width * 1.2, 130, cliente)
        c.drawString(label_width * 1.2, 80, pedido)

        c.save()

    # Remover o arquivo temporário
    os.remove(qr_filename)





def imprimir_pdf(pdf_file):
    conn = cups.Connection()
    printers = conn.getPrinters()
    printer_name = printers.keys()[0]
    job_id = conn.printFile(printer_name,pdf_file,"Etiqueta",{'PageSize': 'Custom.10x2.5cm', 'FitToPage': True, 'Scaling': 100})
    print(f"ID {job_id} enviado para impressão")


criar_pdf("305815-1.pdf", "KIBELUS MODA LTDA", "101603", "305815-1", "BRASPRESS")
imprimir_pdf("305815-1.pdf")
os.remove("305815-1.pdf")