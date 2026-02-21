const { firefox } = require('playwright');
const fs = require('fs');
const path = require('path');

(async () => {
    const args = process.argv.slice(2);
    if (args.length < 3) {
        console.error("Uso: node render-playwright.cjs <html_path> <output_path> <format: image|pdf>");
        process.exit(1);
    }

    const htmlPath = args[0];
    const outputPath = args[1];
    const format = args[2];

    if (!fs.existsSync(htmlPath)) {
        console.error(`Erro: Arquivo HTML não encontrado em ${htmlPath}`);
        process.exit(1);
    }

    let browser;
    try {
        // Inicializa Firefox. O Playwright gerencia seu próprio binário.
        browser = await firefox.launch({
            headless: true,
            // Firefox não requer no-sandbox e lida melhor com restrições OpenVZ
        });

        const context = await browser.newContext({
            viewport: { width: 1080, height: 1920 },
            deviceScaleFactor: 2
        });

        const page = await context.newPage();
        
        // Resolve URL absoluto
        const fileUrl = 'file://' + path.resolve(htmlPath);
        
        // Carrega página e aguarda requisições (fontes/imagens) concluírem
        await page.goto(fileUrl, { waitUntil: 'networkidle', timeout: 30000 });

        if (format === 'image') {
            await page.screenshot({ path: outputPath, fullPage: true, type: 'png' });
        } else if (format === 'pdf') {
            await page.emulateMedia({ media: 'screen' });
            await page.pdf({ 
                path: outputPath, 
                format: 'A4', 
                margin: { top: '10mm', bottom: '10mm', left: '10mm', right: '10mm' },
                printBackground: true
            });
        }

        await browser.close();
        console.log(`SUCESSO: ${outputPath}`);
    } catch (err) {
        console.error("ERRO_PLAYWRIGHT:", err.message);
        if (browser) await browser.close();
        process.exit(1);
    }
})();
