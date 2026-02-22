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

    echo "📥 Puxando do GitHub..."
    
    # Ajustar permissões para permitir que o usuário de deploy sobrescreva os arquivos
    printf "Kellytamo@10\n" | su root -c "chown -R maximooficial:maximooficial /home/maxpdv/public_html && chmod -R 775 /home/maxpdv/public_html"

    git fetch --all
    git reset --hard origin/main
    git pull origin main

    # Restaurar permissões para o www-data poder escrever nos logs/cache
    printf "Kellytamo@10\n" | su root -c "chown -R www-data:www-data /home/maxpdv/public_html/storage /home/maxpdv/public_html/bootstrap/cache && chmod -R 775 /home/maxpdv/public_html/storage /home/maxpdv/public_html/bootstrap/cache"

    echo "🧹 Limpando caches do Laravel..."
    php artisan optimize:clear
    php artisan config:cache
    php artisan view:cache

    echo "✅ Servidor atualizado!"
ENDSSH

echo "✨ Deploy concluído!"