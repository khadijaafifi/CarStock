const puppeteer = require('puppeteer');

async function scrapeModels(url) {
    const browser = await puppeteer.launch({ headless: true });
    const page = await browser.newPage();
    await page.goto(url, { waitUntil: 'networkidle2' });

    // Extraire les modèles de voitures
    const models = await page.$$eval('.list-group-item span.model-name', elements => {
        return elements.map(el => el.textContent.trim());
    });

    await browser.close();
    return models;
}

// Recevoir l'URL en paramètre
const url = process.argv[2];  // Récupérer l'URL passée en argument
scrapeModels(url).then(models => {
    console.log(JSON.stringify(models));  // Retourner les modèles sous forme de JSON
}).catch(error => {
    console.error('Erreur lors du scraping:', error);
    process.exit(1);
});
