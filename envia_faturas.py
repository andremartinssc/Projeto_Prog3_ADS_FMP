import pandas as pd
from reportlab.lib.pagesizes import letter
from reportlab.platypus import SimpleDocTemplate, Paragraph, Spacer, Image
from reportlab.lib.styles import getSampleStyleSheet
from reportlab.lib import colors
import qrcode
from io import BytesIO
from datetime import datetime
from reportlab.lib.units import inch
import os
import logging
import base64
from email.mime.multipart import MIMEMultipart
from email.mime.text import MIMEText
from email.mime.base import MIMEBase
from email import encoders

# Importações para OAuth 2.0
import pickle
from google.auth.transport.requests import Request
from google_auth_oauthlib.flow import InstalledAppFlow
from googleapiclient.discovery import build
from googleapiclient.errors import HttpError

# Configuração do Logging
logging.basicConfig(filename='fatura_email_oauth.log', level=logging.INFO,
                    format='%(asctime)s - %(levelname)s - %(message)s')

# *** CONFIGURAÇÕES (agora do CSV) ***
CSV_ARQUIVO = 'faturas_com_dados_cliente.csv'
CAMINHO_PDFS = './faturas_pdf/'  # Pasta para salvar os PDFs

# Se modificar SCOPES, delete o arquivo token.pickle.
SCOPES = ['https://www.googleapis.com/auth/gmail.send']  # Permissão para enviar e-mails


def gmail_send_message(subject, body, to, file_path=None):
    """Envia um e-mail através do Gmail API com OAuth 2.0."""
    creds = None
    # O arquivo token.pickle armazena os tokens de acesso e atualização do usuário,
    # e é criado automaticamente na primeira vez que o fluxo de autorização é executado.
    if os.path.exists('token.pickle'):
        with open('token.pickle', 'rb') as token:
            creds = pickle.load(token)
    # Se não houver credenciais (ou forem inválidas), faça o usuário fazer login.
    if not creds or not creds.valid:
        if creds and creds.expired and creds.refresh_token:
            creds.refresh(Request())
        else:
            flow = InstalledAppFlow.from_client_secrets_file(
                'credentials.json', SCOPES)  # Certifique-se de que 'credentials.json' está no mesmo diretório
            creds = flow.run_local_server(port=0)
        # Salve as credenciais para a próxima execução.
        with open('token.pickle', 'wb') as token:
            pickle.dump(creds, token)

    try:
        service = build('gmail', 'v1', credentials=creds)
        message = MIMEMultipart()
        message['to'] = to
        message['subject'] = subject
        message.attach(MIMEText(body, 'plain'))

        if file_path:
            try:
                with open(file_path, 'rb') as f:
                    part = MIMEBase('application', 'octet-stream')
                    part.set_payload(f.read())
                encoders.encode_base64(part)
                part.add_header('Content-Disposition', 'attachment', filename=os.path.basename(file_path))
                message.attach(part)
                logging.info(f"Arquivo anexado: {file_path}")
            except FileNotFoundError:
                logging.error(f"Arquivo não encontrado para anexar: {file_path}")
                return False

        create_message = {'raw': base64.urlsafe_b64encode(message.as_bytes()).decode()}
        send_message = (service.users().messages().send(userId="me", body=create_message).execute())
        logging.info(F'Mensagem enviada para {to} Message Id: {send_message["id"]}')
        print(F'Mensagem enviada para {to} Message Id: {send_message["id"]}')
        return True

    except HttpError as error:
        logging.error(F'Ocorreu um erro ao enviar o e-mail: {error}')
        print(F'Ocorreu um erro ao enviar o e-mail: {error}')
        return False


def gerar_pdf_fatura(nome_cliente, cliente_email, cliente_telefone, fatura_valor, fatura_data_vencimento, fatura_id,
                     caminho_pdf):
    """Gera um PDF de fatura com os dados fornecidos e um QR code."""

    nome_arquivo_cliente = ''.join(e for e in nome_cliente if e.isalnum()).lower()
    nome_arquivo = f"fatura_{fatura_id}_{nome_arquivo_cliente}.pdf"
    caminho_completo = os.path.join(caminho_pdf, nome_arquivo)
    doc = SimpleDocTemplate(caminho_completo, pagesize=letter)
    styles = getSampleStyleSheet()
    story = []

    story.append(Paragraph("FATURA", styles['Heading1']))
    story.append(Spacer(1, 0.1 * inch))
    story.append(Paragraph(f"Nome: {nome_cliente}", styles['Normal']))
    story.append(Paragraph(f"Email: {cliente_email}", styles['Normal']))
    story.append(Paragraph(f"Número da Fatura: {fatura_id}", styles['Normal']))
    story.append(Paragraph(f"Valor: R$ {fatura_valor:.2f}", styles['Normal']))
    story.append(
        Paragraph(f"Data de Vencimento: {datetime.strptime(fatura_data_vencimento, '%Y-%m-%d').strftime('%d/%m/%Y')}",
                  styles['Normal']))
    story.append(Spacer(1, 0.1 * inch))

    qr = qrcode.QRCode(version=1, error_correction=qrcode.constants.ERROR_CORRECT_L, box_size=6, border=2)
    qr_code_info = f"Fatura: {fatura_id}\nCliente: {nome_cliente}\nValor: R$ {fatura_valor:.2f}\nVencimento: {datetime.strptime(fatura_data_vencimento, '%Y-%m-%d').strftime('%d/%m/%Y')}"
    qr.add_data(qr_code_info)
    qr.make(fit=True)
    img = qr.make_image(fill_color="black", back_color="white")
    img_buffer = BytesIO()
    img.save(img_buffer)
    img_buffer.seek(0)
    img_reportlab = Image(img_buffer, width=1.0 * inch, height=1.0 * inch)
    story.append(img_reportlab)
    story.append(Spacer(1, 0.1 * inch))

    doc.build(story)
    logging.info(f"Fatura {fatura_id} ({nome_cliente}) gerada como '{nome_arquivo}' com sucesso.")
    print(f"[LOG] Fatura {fatura_id} ({nome_cliente}) gerada como '{nome_arquivo}' com sucesso.")
    return caminho_completo


if __name__ == '__main__':
    logging.info("Iniciando geração de PDFs e envio de e-mails com OAuth 2.0 a partir do CSV...")
    try:
        df_faturas = pd.read_csv(CSV_ARQUIVO, encoding='utf-8')
    except FileNotFoundError:
        logging.error(f"Arquivo CSV não encontrado: {CSV_ARQUIVO}")
        print(f"[ERRO] Arquivo CSV não encontrado: {CSV_ARQUIVO}")
        exit()

    # Filtra apenas as faturas com status "pendente" (usando o nome correto da coluna)
    df_pendentes = df_faturas[df_faturas["Fatura_Status"] == "pendente"].copy()

    print(f"[LOG] {len(df_pendentes)} faturas pendentes encontradas no CSV.")
    logging.info(f"{len(df_pendentes)} faturas pendentes encontradas no CSV.")

    # Certifique-se de que a pasta para os PDFs existe
    os.makedirs(CAMINHO_PDFS, exist_ok=True)

    for index, fatura in df_pendentes.iterrows():
        nome_cliente = fatura["Cliente_Nome"]
        cliente_email = fatura["Cliente_Email"]
        cliente_telefone = fatura["Cliente_Telefone"]
        fatura_valor = fatura["Fatura_Valor"]
        fatura_data_vencimento = fatura["Fatura_Data_Vencimento"]
        fatura_id = fatura["Fatura_ID"]

        pdf_caminho = gerar_pdf_fatura(
            nome_cliente,
            cliente_email,
            cliente_telefone,
            fatura_valor,
            fatura_data_vencimento,
            fatura_id,
            CAMINHO_PDFS
        )

        if pdf_caminho:
            assunto = f"Sua Fatura - Fatura #{fatura_id}"
            corpo = f"Prezado(a) {nome_cliente},\n\nSegue em anexo sua fatura referente ao mês.\n\nAtenciosamente,\nSua Empresa"
            if gmail_send_message(assunto, corpo, cliente_email, pdf_caminho):
                logging.info(f"E-mail com fatura {fatura_id} enviado para {cliente_email} com OAuth 2.0")
                print(f"[LOG] E-mail com fatura {fatura_id} enviado para {cliente_email} com OAuth 2.0")
                # Atualiza o status para "enviado" no DataFrame (usando o nome correto da coluna)
                df_faturas.loc[df_faturas["Fatura_ID"] == fatura_id, "Fatura_Status"] = "enviado"
            else:
                logging.error(f"Falha ao enviar e-mail com fatura {fatura_id} para {cliente_email} (OAuth 2.0)")
                print(f"[ERRO] Falha ao enviar e-mail com fatura {fatura_id} para {cliente_email} (OAuth 2.0)")
        else:
            logging.error(f"Não foi possível gerar o PDF para {nome_cliente} (Fatura #{fatura_id}). E-mail não enviado.")
            print(f"[ERRO] Não foi possível gerar o PDF para {nome_cliente} (Fatura #{fatura_id}). E-mail não enviado.")

    # Salva o DataFrame atualizado de volta no CSV
    try:
        df_faturas.to_csv(CSV_ARQUIVO, encoding='utf-8', index=False)
        logging.info(f"Arquivo CSV '{CSV_ARQUIVO}' atualizado com os status das faturas.")
        print(f"[LOG] Arquivo CSV '{CSV_ARQUIVO}' atualizado com os status das faturas.")
    except Exception as e:
        logging.error(f"Erro ao salvar o arquivo CSV: {e}")
        print(f"[ERRO] Erro ao salvar o arquivo CSV: {e}")

    logging.info("Processo de geração de PDFs e envio de e-mails com OAuth 2.0 concluído.")
    print("[LOG] Processo de geração de PDFs e envio de e-mails com OAuth 2.0 concluído.")