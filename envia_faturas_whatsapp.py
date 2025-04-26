import pandas as pd
from selenium import webdriver
from selenium.webdriver.common.by import By
import time
from datetime import datetime
from reportlab.lib.pagesizes import A4
from reportlab.pdfgen import canvas
from reportlab.lib.utils import ImageReader
import qrcode
import os


# Função para gerar a fatura em PDF com QR Code
def gerar_fatura_pdf(nome, telefone, vencimento, valor):
    nome_arquivo = f"fatura_{telefone}.pdf"
    c = canvas.Canvas(nome_arquivo, pagesize=A4)
    largura, altura = A4

    # Cabeçalho
    c.setFont("Helvetica-Bold", 16)
    c.drawString(100, altura - 100, "Fatura de Pagamento")

    # Dados do cliente
    c.setFont("Helvetica", 12)
    c.drawString(100, altura - 140, f"Cliente: {nome}")
    c.drawString(100, altura - 160, f"Telefone: {telefone}")
    c.drawString(100, altura - 180, f"Vencimento: {vencimento}")
    c.drawString(100, altura - 200, f"Valor: R$ {valor:.2f}")
    c.drawString(100, altura - 240, "Por favor, regularize o pagamento o quanto antes.")
    c.drawString(100, altura - 260, "Dúvidas? Fale conosco.")

    # Geração do QR Code (exemplo com dados fictícios – personalize conforme necessário)
    dados_qr = f"Pagamento PIX - Cliente: {nome} | Valor: R$ {valor:.2f} | Tel: {telefone}"
    qr = qrcode.make(dados_qr)
    qr_img_path = f"qrcode_{telefone}.png"
    qr.save(qr_img_path)

    # Inserção do QR Code no PDF
    c.drawImage(ImageReader(qr_img_path), 100, altura - 420, width=120, height=120)
    c.drawString(100, altura - 440, "Escaneie o QR Code para pagar com PIX.")

    c.save()

    # Remoção da imagem temporária
    os.remove(qr_img_path)

    return os.path.abspath(nome_arquivo)


# Carrega os dados
df = pd.read_csv('faturas_com_dados_cliente.csv')
df.columns = df.columns.str.strip().str.lower()
df['fatura_data_vencimento'] = pd.to_datetime(df['fatura_data_vencimento'], errors='coerce')

# Filtra faturas vencidas
data_hoje = pd.to_datetime(datetime.today().date())
faturas_vencidas = df[df['fatura_data_vencimento'] < data_hoje]

# Inicia o navegador
driver = webdriver.Chrome()
driver.get('https://web.whatsapp.com')
input("Escaneie o QR Code e pressione Enter para continuar...")

# Envia as mensagens via WhatsApp
for index, row in faturas_vencidas.iterrows():
    nome = row['cliente_nome']
    telefone = str(row['cliente_telefone'])
    vencimento = row['fatura_data_vencimento'].strftime('%d/%m/%Y')
    valor = row['fatura_valor']

    # Gera o PDF com QR Code
    caminho_pdf = gerar_fatura_pdf(nome, telefone, vencimento, valor)

    # Monta a mensagem
    mensagem = (
        f"Olá {nome}, identificamos que sua fatura no valor de R$ {valor:.2f}, "
        f"com vencimento em {vencimento}, está em atraso. "
        f"Por favor, regularize o pagamento o quanto antes.\n\n"
        f"📎 A fatura com QR Code para pagamento foi gerada. {caminho_pdf}"
    )

    print(f"\nEnviando mensagem para {nome} ({telefone})...")

    # Acessa o WhatsApp com mensagem pré-preenchida
    url = f"https://web.whatsapp.com/send?phone={telefone}&text={mensagem}"
    driver.get(url)
    time.sleep(15)

    try:
        # Clica no botão enviar
        driver.find_element(By.XPATH, '//span[@data-icon="send"]').click()
        print("Mensagem enviada com sucesso.")
    except Exception as e:
        print(f"Erro ao enviar mensagem para {telefone}: {e}")

print("Todas as mensagens foram enviadas.")
time.sleep(10)
driver.quit()
