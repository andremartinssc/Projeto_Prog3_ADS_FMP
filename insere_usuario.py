import rpa as r
import pyautogui as p
import pandas as pd

r.init(visual_automation=True, chrome_browser=True)

url_cadastro = 'http://localhost/Gerenciamento_Cobranca_A1/public/cadastro_cliente.php'
excel_file = 'clientes.xlsx'  # Nome do seu arquivo Excel
sheet_name = 'clientes'  # Nome da planilha no Excel

try:
    df = pd.read_excel(excel_file, sheet_name=sheet_name)

    for index, row in df.iterrows():
        nome = str(row['nome'])  # Certifique-se de que a coluna 'Nome' existe
        email = str(row['email'])  # Certifique-se de que a coluna 'Email' existe
        telefone = str(row['telefone'])

        r.url(url_cadastro)
        p.sleep(3)
        janela = p.getActiveWindow()
        # janela.maximize()
        p.sleep(2)

        # Preenchendo os campos do formulário com dados do Excel
        r.type('//*[@id="nome"]', nome)
        p.sleep(0.5)
        r.type('//*[@id="email"]', email)
        p.sleep(0.5)
        r.type('//*[@id="telefone"]', telefone)
        p.sleep(1)

        # Clicando no botão "Cadastrar"
        r.click('//button[@type="submit"]')
        p.sleep(5)

        print(f"Cliente {index + 1} cadastrado: Nome={nome}, Email={email}, Telefone={telefone}")

except FileNotFoundError:
    print(f"Erro: O arquivo '{excel_file}' não foi encontrado.")
except KeyError as e:
    print(f"Erro: A coluna '{e}' não foi encontrada no arquivo Excel.")
except Exception as e:
    print(f"Ocorreu um erro: {e}")

finally:
    r.close()