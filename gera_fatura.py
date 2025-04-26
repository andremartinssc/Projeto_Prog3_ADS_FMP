from reportlab.lib.pagesizes import letter
from reportlab.platypus import SimpleDocTemplate, Paragraph, Spacer, Image
from reportlab.lib.styles import getSampleStyleSheet
from reportlab.lib import colors
import qrcode
from io import BytesIO
from datetime import datetime
import mysql.connector

# *** CONFIGURAÇÕES DO BANCO DE DADOS ***
DB_HOST = "localhost"
DB_USER = "root"
DB_PASSWORD = ""
DB_NAME = "cobranca_bd"

def buscar_faturas_do_banco():
    """Busca os dados das faturas do banco de dados."""
    try:
        mydb = mysql.connector.connect(
            host=DB_HOST,
            user=DB_USER,
            password=DB_PASSWORD,
            database=DB_NAME
        )
        mycursor = mydb.cursor(dictionary=True)
        query = "SELECT f.id AS numero_fatura, c.nome AS nome_cliente, f.valor, f.data_vencimento " \
                "FROM faturas f JOIN clientes c ON f.cliente_id = c.id"
        mycursor.execute(query)
        faturas = mycursor.fetchall()
        return faturas
    except mysql.connector.Error as err:
        print(f"[ERRO] Erro ao conectar ou buscar dados do banco: {err}")
        return []
    finally:
        if 'mydb' in locals() and mydb.is_connected():
            mycursor.close()
            mydb.close()

def gerar_pdf_fatura(nome, valor, data_vencimento, numero_fatura):
    """Gera um PDF de fatura com nome, valor, data de vencimento e QR Code."""
    # Remove caracteres especiais e espaços do nome para usar no nome do arquivo
    nome_arquivo_cliente = ''.join(e for e in nome if e.isalnum()).lower()
    nome_arquivo = f"fatura_{numero_fatura}_{nome_arquivo_cliente}.pdf"
    doc = SimpleDocTemplate(nome_arquivo, pagesize=letter)
    styles = getSampleStyleSheet()
    story = []

    # Título
    story.append(Paragraph("FATURA", styles['Heading1']))
    story.append(Spacer(1, 12))

    # Informações da Fatura
    story.append(Paragraph(f"Número da Fatura: {numero_fatura}", styles['Normal']))
    story.append(Paragraph(f"Nome: {nome}", styles['Normal']))
    story.append(Paragraph(f"Valor: R$ {valor:.2f}", styles['Normal']))
    story.append(Paragraph(f"Data de Vencimento: {data_vencimento.strftime('%d/%m/%Y')}", styles['Normal']))
    story.append(Spacer(1, 12))

    # Geração do QR Code
    qr = qrcode.QRCode(
        version=1,
        error_correction=qrcode.constants.ERROR_CORRECT_L,
        box_size=10,
        border=4,
    )
    qr_code_info = f"Fatura: {numero_fatura}\nCliente: {nome}\nValor: R$ {valor:.2f}\nVencimento: {data_vencimento.strftime('%d/%m/%Y')}"
    qr.add_data(qr_code_info)
    qr.make(fit=True)

    img = qr.make_image(fill_color="black", back_color="white")

    img_buffer = BytesIO()
    img.save(img_buffer)
    img_buffer.seek(0)

    img_reportlab = Image(img_buffer, width=100, height=100)
    story.append(img_reportlab)
    story.append(Spacer(1, 12))

    doc.build(story)
    print(f"[LOG] Fatura {numero_fatura} ({nome}) gerada como '{nome_arquivo}' com sucesso.")

if __name__ == '__main__':
    print("[LOG] Buscando dados das faturas do banco de dados...")
    lista_faturas_do_banco = buscar_faturas_do_banco()

    if lista_faturas_do_banco:
        print(f"[LOG] {len(lista_faturas_do_banco)} faturas encontradas.")
        for fatura in lista_faturas_do_banco:
            gerar_pdf_fatura(
                fatura["nome_cliente"],
                fatura["valor"],
                fatura["data_vencimento"],
                fatura["numero_fatura"]
            )
        print("[LOG] Geração de PDFs das faturas do banco de dados concluída.")
    else:
        print("[LOG] Nenhuma fatura encontrada no banco de dados.")