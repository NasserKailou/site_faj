<?php
$page_title = 'FAQ';
require_once '../includes/config.php';
require_once '../includes/header.php';

$faqs = [
    ['q'=>'Comment faire un don au FAJ Niger ?','r'=>'Rendez-vous sur la page <a href="'.SITE_URL.'/don">Faire un Don</a>. Vous pouvez payer par Orange Money, Moov Money, carte Visa/Mastercard ou virement bancaire.'],
    ['q'=>'Mon don est-il déductible des impôts ?','r'=>'Les dons au FAJ peuvent ouvrir droit à des déductions fiscales selon la législation nigérienne en vigueur. Contactez-nous pour plus d\'informations.'],
    ['q'=>'Comment puis-je suivre l\'utilisation de mon don ?','r'=>'Vous recevrez un reçu par email. Vous pouvez aussi suivre l\'avancement des projets financés sur la page <a href="'.SITE_URL.'/projets">Nos Projets</a>.'],
    ['q'=>'Puis-je dédier mon don à un projet spécifique ?','r'=>'Oui ! Sur le formulaire de don, vous pouvez choisir le projet auquel vous souhaitez contribuer parmi les projets actifs.'],
    ['q'=>'Le paiement en ligne est-il sécurisé ?','r'=>'Absolument. Nous utilisons des solutions de paiement certifiées (CinetPay, Stripe) avec chiffrement SSL. Vos données bancaires ne sont jamais stockées sur nos serveurs.'],
    ['q'=>'Comment contacter le FAJ Niger ?','r'=>'Via notre <a href="'.SITE_URL.'/contact">formulaire de contact</a>, par email à '.SITE_EMAIL.' ou par téléphone au '.SITE_PHONE.'.'],
    ['q'=>'Puis-je annuler ou rembourser un don ?','r'=>'Les dons sont en principe définitifs. En cas d\'erreur, contactez-nous dans les 48h suivant votre don à '.SITE_EMAIL.'.'],
    ['q'=>'Comment s\'impliquer autrement que financièrement ?','r'=>'Vous pouvez parler du FAJ autour de vous, partager nos actualités sur les réseaux sociaux, ou contacter notre équipe pour explorer des opportunités de bénévolat.'],
];
?>

<div class="page-hero">
    <div class="container page-hero-content">
        <div class="breadcrumb">
            <a href="<?= SITE_URL ?>/">Accueil</a>
            <span class="separator"><i class="fas fa-chevron-right"></i></span>
            <span class="current">FAQ</span>
        </div>
        <h1>Questions <span style="color:var(--secondary)">Fréquentes</span></h1>
        <p>Trouvez rapidement les réponses à vos questions</p>
    </div>
</div>

<section style="padding:80px 0; background:var(--light);">
    <div class="container" style="max-width:800px;">
        <div style="display:flex; flex-direction:column; gap:16px;">
            <?php foreach ($faqs as $i => $faq): ?>
            <div data-aos="fade-up" style="background:white; border-radius:var(--radius); box-shadow:var(--shadow); overflow:hidden;">
                <button onclick="toggleFaq(<?= $i ?>)" style="width:100%; display:flex; justify-content:space-between; align-items:center; padding:20px 24px; background:none; border:none; cursor:pointer; font-family:var(--font-body); font-size:16px; font-weight:600; color:var(--primary); text-align:left;">
                    <?= htmlspecialchars($faq['q']) ?>
                    <i class="fas fa-chevron-down" id="icon-<?= $i ?>" style="transition:transform 0.3s; flex-shrink:0; margin-left:12px; color:var(--secondary);"></i>
                </button>
                <div id="answer-<?= $i ?>" style="display:none; padding:0 24px 20px; color:var(--gray); line-height:1.8; border-top:1px solid var(--light-gray);">
                    <p style="padding-top:16px;"><?= $faq['r'] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div style="text-align:center; margin-top:50px; padding:40px; background:white; border-radius:var(--radius-lg); box-shadow:var(--shadow);" data-aos="fade-up">
            <i class="fas fa-question-circle" style="font-size:40px; color:var(--secondary); margin-bottom:16px; display:block;"></i>
            <h3 style="color:var(--primary); margin-bottom:10px;">Vous n'avez pas trouvé votre réponse ?</h3>
            <p style="color:var(--gray); margin-bottom:24px;">Notre équipe est là pour vous aider.</p>
            <a href="<?= SITE_URL ?>/contact" class="btn btn-primary"><i class="fas fa-envelope"></i> Contactez-nous</a>
        </div>
    </div>
</section>

<script>
function toggleFaq(i) {
    const ans = document.getElementById('answer-'+i);
    const ico = document.getElementById('icon-'+i);
    const open = ans.style.display === 'block';
    ans.style.display = open ? 'none' : 'block';
    ico.style.transform = open ? 'rotate(0deg)' : 'rotate(180deg)';
}
</script>

<?php require_once '../includes/footer.php'; ?>
