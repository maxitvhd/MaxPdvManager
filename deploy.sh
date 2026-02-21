#!/bin/bash

# Configurações
SSH_USER="maximooficial"
SSH_HOST="pdv.aiconect.com.br"
REMOTE_PATH="/home/maxpdv/public_html" 

echo "🚀 Iniciando Sincronização Local -> GitHub -> Hospedagem"

# --- PASSO 1: LOCAL PARA GITHUB ---
echo "📦 1. Enviando alterações locais para o GitHub..."
git add .
read -p "Mensagem do commit: " commit_msg
if [ -z "$commit_msg" ]; then
    commit_msg="update $(date +'%Y-%m-%d %H:%M')"
fi
git commit -m "$commit_msg"
git push origin main

# --- PASSO 2: GITHUB PARA HOSPEDAGEM ---
echo "🌐 2. Conectando na Hospedagem para puxar do GitHub..."

ssh -t $SSH_USER@$SSH_HOST << EOF
    cd $REMOTE_PATH
    
    # Configura para o servidor não pedir senha (se o helper estiver ativo)
    git config --global credential.helper store
    
    echo "📥 Forçando sincronização com a versão do GitHub..."
    git fetch --all
    # O comando abaixo deleta qualquer lixo/mudança na hospedagem e espelha o GitHub
    git reset --hard origin/main 

    echo "📦 Atualizando dependências e banco..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
    npm ci --omit=dev 2>/dev/null || npm install --omit=dev
    npx playwright install firefox
    php artisan migrate --force
    php artisan storage:link 2>/dev/null || true

    echo "🧹 Limpando Caches..."
    php artisan optimize:clear
    php artisan config:cache
    php artisan view:cache

    echo "✅ Hospedagem atualizada com sucesso!"
EOF

echo "✨ Tudo pronto!"