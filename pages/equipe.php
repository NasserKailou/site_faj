<?php
$page_title = 'Notre Équipe';
require_once '../includes/config.php';

try {
    $pdo = getDB();
    $membres = $pdo->query("SELECT * FROM equipe WHERE actif=1 ORDER BY ordre ASC")->fetchAll();
} catch (Exception $e) {
    $membres = [];
}

require_once '../includes/header.php';
?>

<div class="page-hero">
    <div class="container page-hero-content">
        <div class="breadcrumb">
            <a href="<?= SITE_URL ?>/">Accueil</a>
            <span class="separator"><i class="fas fa-chevron-right"></i></span>
            <span class="current">Notre Équipe</span>
        </div>
        <h1>Notre <span style="color:var(--secondary)">Équipe</span></h1>
        <p>Les femmes et hommes engagés pour une justice accessible à tous au Niger</p>
    </div>
</div>

<section style="padding:80px 0; background:var(--light);">
    <div class="container">
        <?php if (empty($membres)): ?>
        <!-- Équipe par défaut -->
        <?php
        $membres_defaut = [
            ['nom'=>'M. Aboubacar MAHAMADOU','poste'=>'Président du Conseil d\'Administration','biographie'=>'Magistrat de haut rang avec plus de 25 ans d\'expérience dans le système judiciaire nigérien.','photo'=>null],
            ['nom'=>'Mme Fatouma IBRAHIM','poste'=>'Directrice Générale','biographie'=>'Juriste et gestionnaire expérimentée, ancienne conseillère au Ministère de la Justice.','photo'=>null],
            ['nom'=>'M. Moussa SANI','poste'=>'Directeur Financier','biographie'=>'Expert financier spécialisé dans la gestion des fonds publics et privés.','photo'=>null],
            ['nom'=>'Mme Aïcha OUMAROU','poste'=>'Responsable Programmes','biographie'=>'Spécialiste en développement avec une expertise en droits humains et accès à la justice.','photo'=>null],
        ];
        $membres = $membres_defaut;
        ?>
        <?php endif; ?>

        <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(260px,1fr)); gap:30px;">
            <?php foreach ($membres as $m): ?>
            <div data-aos="fade-up" style="background:white; border-radius:var(--radius-lg); overflow:hidden; box-shadow:var(--shadow); text-align:center; transition:var(--transition);">
                <div style="height:220px; background:linear-gradient(135deg,var(--primary),var(--primary-light)); display:flex; align-items:center; justify-content:center; position:relative;">
                    <?php if (!empty($m['photo'])): ?>
                    <img src="<?= UPLOADS_URL ?>/equipe/<?= $m['photo'] ?>" alt="<?= sanitize($m['nom']) ?>" style="width:100%; height:100%; object-fit:cover; position:absolute; inset:0;">
                    <?php else: ?>
                    <div style="width:100px; height:100px; border-radius:50%; background:rgba(255,255,255,0.15); display:flex; align-items:center; justify-content:center; font-size:40px; color:white; font-weight:700;">
                        <?= strtoupper(substr($m['nom'], 0, 1)) ?>
                    </div>
                    <?php endif; ?>
                </div>
                <div style="padding:24px 20px;">
                    <h3 style="font-size:17px; font-weight:700; color:var(--primary); margin-bottom:6px;"><?= sanitize($m['nom']) ?></h3>
                    <p style="font-size:13px; color:var(--secondary); font-weight:600; margin-bottom:12px;"><?= sanitize($m['poste']) ?></p>
                    <?php if (!empty($m['biographie'])): ?>
                    <p style="font-size:13px; color:var(--gray); line-height:1.6;"><?= sanitize($m['biographie']) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($m['email']) || !empty($m['linkedin'])): ?>
                    <div style="display:flex; gap:10px; justify-content:center; margin-top:16px;">
                        <?php if (!empty($m['email'])): ?>
                        <a href="mailto:<?= $m['email'] ?>" style="width:34px;height:34px;border-radius:50%;background:var(--light);display:flex;align-items:center;justify-content:center;color:var(--primary);font-size:14px;"><i class="fas fa-envelope"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($m['linkedin'])): ?>
                        <a href="<?= $m['linkedin'] ?>" target="_blank" style="width:34px;height:34px;border-radius:50%;background:var(--light);display:flex;align-items:center;justify-content:center;color:var(--primary);font-size:14px;"><i class="fab fa-linkedin-in"></i></a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- CTA -->
        <div style="text-align:center; margin-top:70px; padding:50px; background:linear-gradient(135deg,var(--primary),var(--primary-light)); border-radius:var(--radius-lg);" data-aos="fade-up">
            <h3 style="font-family:var(--font-display); font-size:28px; color:white; margin-bottom:12px;">Rejoignez notre cause</h3>
            <p style="color:rgba(255,255,255,0.78); font-size:16px; margin-bottom:30px;">Ensemble, construisons un système judiciaire plus juste pour le Niger.</p>
            <a href="<?= SITE_URL ?>/don" class="btn btn-primary btn-lg" style="background:var(--secondary); box-shadow:0 4px 20px rgba(232,135,10,0.5);">
                <i class="fas fa-heart"></i> Faire un Don
            </a>
        </div>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>
