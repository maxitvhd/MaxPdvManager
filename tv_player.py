import flet as ft
import requests
import time
import threading

class TvDoorPlayer:
    def __init__(self, page: ft.Page):
        self.page = page
        self.page.title = "TvDoor Player"
        self.page.bgcolor = ft.colors.BLACK
        self.page.window_full_screen = True
        self.base_url = "http://localhost:8000" # Mudar para seu domínio real
        self.token = None
        self.playlist = []
        self.current_index = 0
        
        self.screen = ft.Container(expand=True, content=ft.Text("Iniciando...", size=30, color="white"))
        self.page.add(self.screen)
        
        self.check_local_token()

    def check_local_token(self):
        # Simples demonstração: aqui poderia ler de um arquivo .env ou db local
        if not self.token:
            self.show_pairing_screen()
        else:
            self.start_sync()

    def show_pairing_screen(self):
        # Em um player real, obteríamos um hardware ID
        code = "AGUARDE" 
        self.screen.content = ft.Column([
            ft.Text("PAREAMENTO TVDOOR", size=40, weight="bold", color="white"),
            ft.Text("Vá ao seu painel administrativo e use o código gerado lá.", size=20, color="white"),
            ft.Text("ID do Dispositivo: PLAY-001", size=15, color="gray"),
            ft.ProgressRing()
        ], horizontal_alignment="center", alignment="center")
        self.page.update()

    def start_sync(self):
        threading.Thread(target=self.sync_loop, daemon=True).start()

    def sync_loop(self):
        while True:
            try:
                headers = {"X-Device-Token": self.token}
                response = requests.get(f"{self.base_url}/tvdoor/api/sync", headers=headers)
                data = response.json()
                if data["success"]:
                    self.playlist = data["playlist"]
                    if not self.is_playing:
                        self.play_next()
            except Exception as e:
                print(f"Erro sync: {e}")
            time.sleep(60)

    def play_next(self):
        # Lógica de exibição usando controles do Flet (Image, Video, Row/Column para layouts)
        pass

def main(page: ft.Page):
    TvDoorPlayer(page)

if __name__ == "__main__":
    ft.app(target=main)
