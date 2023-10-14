import cups
import os
from reportlab.lib.pagesizes import landscape
from reportlab.lib.units import cm
from reportlab.pdfgen import canvas
import tempfile
from reportlab.graphics import barcode
import qrcode


def criar_pdf(saida_pdf, titulo, cliente, pedido, transportadora, separador, agrupamento):
    # Configurações das etiquetas e colunas
    label_width = 7.5 * cm
    label_height = 1.8 * cm

    # Criar o PDF e ajustar o tamanho da página para paisagem com tamanho personalizado
    custom_page_size = landscape((label_width, label_height))

    # Criar um arquivo temporário para salvar o QR code
    with tempfile.NamedTemporaryFile(delete=False, suffix=".png") as temp_qr_file:
        qr_filename = temp_qr_file.name

        c = canvas.Canvas(saida_pdf, pagesize=custom_page_size)

        # Título centralizado
        c.setFont("Helvetica-Bold", 9)
        title = titulo
        c.drawString(0.3 * cm, 1.5 * cm, title)


        qr = qrcode.QRCode(version=1, box_size=int(1.72 * cm), border=0)
        qr.add_data(cliente)  # Substitua pelo link desejado
        qr.make(fit=True)
        qr_img = qr.make_image(fill_color="black", back_color="white")
        qr_img.save(qr_filename)  # Salvar a imagem do QR code no arquivo temporário
        c.drawImage(qr_filename, 5.2 * cm, 0.43 * cm, width=1.45 * cm, height= 1.30 * cm)

        #barcode_value = cliente  # Substitua pelo valor do código de barras desejado
        #barcode_code128 = barcode.code128.Code128(barcode_value, barHeight=15, humanReadable=True)
        # Desenhar o código de barras diretamente no canvas
        #barcode_code128.drawOn(c, 3.0 * cm, 0.3 * cm)

        c.setFont("Helvetica", 9)
        c.drawString(0.3 * cm, 1.1 * cm, "Nº Cliente:")
        c.drawString(0.3 * cm, 0.8 * cm, "Nº Pedido:")
        c.drawString(0.3 * cm, 0.50 * cm, transportadora)

        c.setFont("Helvetica-Bold", 8)
        c.drawString(0.3 * cm, 0.1 * cm, separador)


        c.drawString(2.0 * cm, 1.1 * cm, cliente)
        c.drawString(2.0 * cm, 0.8 * cm, pedido)
        c.setFont("Helvetica-Bold", 5)
        c.drawString(2.5 * cm, 0.1 * cm, agrupamento)

        c.save()

def imprimir_pdf(pdf_file):
    conn = cups.Connection()
    printers = conn.getPrinters()
    printer_name = list(printers.keys())[0]
    job_id = conn.printFile(printer_name,pdf_file,"Etiqueta",{'PageSize': 'Custom.10x0.25cm', 'FitToPage': 'True', 'Scaling': '100','Orientation':'3'})
    print(f"ID {job_id} enviado para impressão")

def EtiquetaPrateleira(saida_pdf,endereco, rua,modulo,posicao):
    # Configurações das etiquetas e colunas
    label_width = 7.5 * cm
    label_height = 1.8 * cm

    # Criar o PDF e ajustar o tamanho da página para paisagem com tamanho personalizado
    custom_page_size = landscape((label_width, label_height))
    with tempfile.NamedTemporaryFile(delete=False, suffix=".png") as temp_qr_file:
        qr_filename = temp_qr_file.name

        c = canvas.Canvas(saida_pdf, pagesize=custom_page_size)

        # Título centralizado
        title = rua
        c.setFont("Helvetica-Bold", 19)
        c.drawString(0.3 * cm, 3.5 * cm, title)

        c.setFont("Helvetica-Bold", 9)
        c.drawString(0.3 * cm, 1.5 * cm, 'Rua.')

        title = modulo
        c.setFont("Helvetica-Bold", 19)
        c.drawString(0.9 * cm, 3.5 * cm, title)

        c.setFont("Helvetica-Bold", 9)
        c.drawString(0.9 * cm, 1.5 * cm, 'Modulo.')

        title = posicao
        c.setFont("Helvetica-Bold", 19)
        c.drawString(1.9 * cm, 3.5 * cm, title)

        c.setFont("Helvetica-Bold", 9)
        c.drawString(1.9 * cm, 1.5 * cm, 'Posicao.')


        qr = qrcode.QRCode(version=1, box_size=int(1.72 * cm), border=0)
        qr.add_data(endereco)  # Substitua pelo link desejado
        qr.make(fit=True)
        qr_img = qr.make_image(fill_color="black", back_color="white")
        qr_img.save(qr_filename)  # Salvar a imagem do QR code no arquivo temporário
        c.drawImage(qr_filename, 5.2 * cm, 0.43 * cm, width=1.45 * cm, height= 1.30 * cm)

        c.save()
