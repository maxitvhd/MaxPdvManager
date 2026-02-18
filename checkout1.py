import tkinter as tk
from tkinter import ttk, messagebox
import requests
import json
from datetime import datetime

class PDVInterface:
    def __init__(self, root):
        self.root = root
        self.root.title("Sistema PDV")
        self.root.geometry("1200x700")
        
        # Configuração da API
        self.api_url = "http://sua-api-url/api"
        self.token = None
        
        # Criar o layout principal
        self.create_login_frame()
        self.create_main_frame()
        
        # Inicialmente mostrar apenas o login
        self.login_frame.pack(fill='both', expand=True)
        self.main_frame.pack_forget()
        
    def create_login_frame(self):
        self.login_frame = ttk.Frame(self.root, padding="20")
        
        # Elementos do login
        ttk.Label(self.login_frame, text="Login do Sistema", font=('Helvetica', 16, 'bold')).pack(pady=20)
        
        ttk.Label(self.login_frame, text="Chave de Licença:").pack()
        self.license_key = ttk.Entry(self.login_frame, width=40)
        self.license_key.pack(pady=5)
        
        ttk.Button(self.login_frame, text="Conectar", command=self.authenticate).pack(pady=20)
        
    def create_main_frame(self):
        self.main_frame = ttk.Frame(self.root)
        
        # Frame superior para pesquisa
        search_frame = ttk.Frame(self.main_frame)
        search_frame.pack(fill='x', padx=10, pady=5)
        
        ttk.Label(search_frame, text="Pesquisar Produto:").pack(side='left')
        self.search_entry = ttk.Entry(search_frame, width=40)
        self.search_entry.pack(side='left', padx=5)
        ttk.Button(search_frame, text="Buscar", command=self.search_products).pack(side='left')
        
        # Frame principal dividido em dois
        content_frame = ttk.Frame(self.main_frame)
        content_frame.pack(fill='both', expand=True, padx=10, pady=5)
        
        # Lista de produtos
        products_frame = ttk.Frame(content_frame)
        products_frame.pack(side='left', fill='both', expand=True)
        
        # Criar Treeview para produtos
        self.tree = ttk.Treeview(products_frame, columns=('ID', 'Nome', 'Preço', 'Estoque'), show='headings')
        self.tree.heading('ID', text='ID')
        self.tree.heading('Nome', text='Nome')
        self.tree.heading('Preço', text='Preço')
        self.tree.heading('Estoque', text='Estoque')
        
        # Configurar colunas
        self.tree.column('ID', width=50)
        self.tree.column('Nome', width=200)
        self.tree.column('Preço', width=100)
        self.tree.column('Estoque', width=100)
        
        # Adicionar scrollbar
        scrollbar = ttk.Scrollbar(products_frame, orient='vertical', command=self.tree.yview)
        self.tree.configure(yscrollcommand=scrollbar.set)
        
        self.tree.pack(side='left', fill='both', expand=True)
        scrollbar.pack(side='right', fill='y')
        
    def authenticate(self):
        key = self.license_key.get()
        
        try:
            headers = {
                'Authorization': f'Bearer {key}',
                'Content-Type': 'application/json'
            }
            
            # Dados da máquina (você pode expandir isso)
            machine_data = {
                'codigo': 'PDV001',
                'sistema_operacional': 'Windows',
                'versao_sistema': '10',
                'arquitetura': '64',
                'hostname': 'PDV-LOCAL',
                'ip': '127.0.0.1',
                'mac': '00:00:00:00:00:00'
            }
            
            response = requests.post(
                f"{self.api_url}/conectar",
                headers=headers,
                json=machine_data
            )
            
            if response.status_code == 200:
                data = response.json()
                self.token = key
                self.login_frame.pack_forget()
                self.main_frame.pack(fill='both', expand=True)
                self.load_products(data.get('produtos', []))
                messagebox.showinfo("Sucesso", "Conectado com sucesso!")
            else:
                messagebox.showerror("Erro", "Falha na autenticação")
                
        except Exception as e:
            messagebox.showerror("Erro", f"Erro de conexão: {str(e)}")
    
    def load_products(self, products):
        # Limpar tabela atual
        for item in self.tree.get_children():
            self.tree.delete(item)
            
        # Carregar novos produtos
        for product in products:
            self.tree.insert('', 'end', values=(
                product.get('id'),
                product.get('nome'),
                f"R$ {product.get('preco', 0.0):.2f}",
                product.get('estoque', 0)
            ))
    
    def search_products(self):
        search_term = self.search_entry.get()
        # Implementar lógica de busca
        # Este é um exemplo básico - você precisará adaptar à sua API
        try:
            headers = {
                'Authorization': f'Bearer {self.token}',
                'Content-Type': 'application/json'
            }
            
            response = requests.get(
                f"{self.api_url}/produtos/buscar",
                headers=headers,
                params={'termo': search_term}
            )
            
            if response.status_code == 200:
                products = response.json()
                self.load_products(products)
            else:
                messagebox.showerror("Erro", "Falha ao buscar produtos")
                
        except Exception as e:
            messagebox.showerror("Erro", f"Erro de conexão: {str(e)}")

if __name__ == "__main__":
    root = tk.Tk()
    app = PDVInterface(root)
    root.mainloop()