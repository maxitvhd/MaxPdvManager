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

ssh -t $SSH_USER@$SSH_HOST << 'ENDSSH'
    cd /home/maxpdv/public_html

    git config --global credential.helper store

    SUDO_PASS="Kellytamo@10"

    echo "🔓 Liberando permissões para o git atualizar os arquivos..."
    echo "$SUDO_PASS" | sudo -S chmod -R 777 storage/ bootstrap/cache/ 2>/dev/null || true

    echo "📥 Forçando sincronização com a versão do GitHub..."
    git fetch --all
    git reset --hard origin/main

    echo "📦 Atualizando dependências e banco..."
    composer install --no-interaction --prefer-dist --optimize-autoloader 2>/dev/null
    npm ci --omit=dev 2>/dev/null || npm install --omit=dev 2>/dev/null || true

    php artisan migrate --force
    php artisan storage:link 2>/dev/null || true

    echo "🔒 Restaurando permissões corretas para o servidor web..."
    echo "$SUDO_PASS" | sudo -S chmod -R 775 storage/ bootstrap/cache/
    echo "$SUDO_PASS" | sudo -S chown -R www-data:www-data storage/ bootstrap/cache/ 2>/dev/null || \
    echo "$SUDO_PASS" | sudo -S chown -R maximooficial:maximooficial storage/ bootstrap/cache/ 2>/dev/null || true

    echo "🧹 Limpando Caches..."
    php artisan optimize:clear
    php artisan config:cache
    php artisan view:cache

    echo "🎨 Registrando temas novos (se ainda não existirem)..."
    php artisan tinker --execute="
        if (!\App\Models\MaxDivulgaTheme::where('identifier','azul_ofertas')->exists()) {
            \App\Models\MaxDivulgaTheme::create([
                'name' => 'Azul Ofertas Premium',
                'identifier' => 'azul_ofertas',
                'path' => 'maxdivulga.themes.azul_ofertas',
                'description' => 'Tema azul e amarelo premium.',
                'is_active' => true,
                'min_products' => 1,
                'max_products' => 15,
            ]);
            echo 'Tema azul criado!';
        } else {
            echo 'Tema azul ja existe.';
        }
    " 2>/dev/null || true

    echo "✅ Hospedagem atualizada com sucesso!"
ENDSSH

echo "✨ Tudo pronto!"