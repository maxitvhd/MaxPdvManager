#!/bin/bash

SSH_USER="maximooficial"
SSH_HOST="pdv.aiconect.com.br"

echo "🚀 Iniciando Deploy Local -> GitHub -> Hospedagem"

# --- PASSO 1: LOCAL PARA GITHUB ---
echo "📦 Enviando para o GitHub..."
git add .
read -p "Mensagem do commit: " commit_msg
if [ -z "$commit_msg" ]; then
    commit_msg="deploy $(date +'%Y-%m-%d %H:%M')"
fi
git commit -m "$commit_msg" && git push origin main || echo "ℹ️  Nada novo para enviar."

# --- PASSO 2: SERVIDOR PUXA DO GITHUB E LIMPA CACHES ---
echo "🌐 Atualizando servidor..."

ssh $SSH_USER@$SSH_HOST << 'ENDSSH'
    cd /home/maxpdv/public_html

    # Passo A: Dar permissão total ao usuário de deploy (maximooficial)
    # Note: Removi o grupo :maximooficial que estava dando erro "invalid group"
    printf "Kellytamo@10\n" | su root -c "chown -R maximooficial /home/maxpdv/public_html && chmod -R 775 /home/maxpdv/public_html"

    echo "📥 Sincronizando com GitHub..."
    git fetch --all
    git reset --hard origin/main
    git clean -fd # Remove arquivos não rastreados que impedem o pull
    git pull origin main

    # Passo B: Ajustar permissões para o servidor Web (www-data)
    # Usamos 777 na storage pra evitar conflitos entre o CLI (deploy) e o Web (site)
    printf "Kellytamo@10\n" | su root -c "chown -R www-data:www-data /home/maxpdv/public_html/storage /home/maxpdv/public_html/bootstrap/cache && chmod -R 777 /home/maxpdv/public_html/storage /home/maxpdv/public_html/bootstrap/cache"

    echo "🧹 Limpando caches do Laravel..."
    # Rodar como sudo para garantir que o maximooficial consiga limpar arquivos do www-data se sobrarem
    php artisan optimize:clear
    php artisan config:cache
    php artisan view:cache
    php artisan migrate

    echo "✅ Servidor atualizado!"
ENDSSH

echo "✨ Deploy concluído!"