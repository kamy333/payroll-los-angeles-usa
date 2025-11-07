<?php

declare(strict_types=1);

use App\Repositories\I18nRepository;

require __DIR__ . '/../../vendor/autoload.php';

$app = require __DIR__ . '/../../app/bootstrap.php';

/** @var I18nRepository $repository */
$repository = $app->getContainer()->get(I18nRepository::class);

$translations = [
    'app.title' => [
        'context' => 'layout',
        'values' => [
            'en' => 'Household payroll',
            'es' => 'Nómina doméstica',
            'pt_BR' => 'Folha doméstica',
            'fr' => 'Paie à domicile',
            'de' => 'Haushaltslohnabrechnung',
            'it' => 'Paghe domestiche',
        ],
    ],
    'app.brand' => [
        'context' => 'layout',
        'values' => [
            'en' => 'HomePay Hub',
            'es' => 'HomePay Hub',
            'pt_BR' => 'HomePay Hub',
            'fr' => 'HomePay Hub',
            'de' => 'HomePay Hub',
            'it' => 'HomePay Hub',
        ],
    ],
    'layout.close_menu' => [
        'context' => 'layout',
        'values' => [
            'en' => 'Close menu',
            'es' => 'Cerrar menú',
            'pt_BR' => 'Fechar menu',
            'fr' => 'Fermer le menu',
            'de' => 'Menü schließen',
            'it' => 'Chiudi menu',
        ],
    ],
    'layout.open_menu' => [
        'context' => 'layout',
        'values' => [
            'en' => 'Open menu',
            'es' => 'Abrir menú',
            'pt_BR' => 'Abrir menu',
            'fr' => 'Ouvrir le menu',
            'de' => 'Menü öffnen',
            'it' => 'Apri menu',
        ],
    ],
    'nav.employee_header' => [
        'context' => 'navigation',
        'values' => [
            'en' => 'Employee',
            'es' => 'Empleado',
            'pt_BR' => 'Colaborador',
            'fr' => 'Employé',
            'de' => 'Mitarbeiter',
            'it' => 'Dipendente',
        ],
    ],
    'nav.employee_dashboard' => [
        'context' => 'navigation',
        'values' => [
            'en' => 'My dashboard',
            'es' => 'Mi panel',
            'pt_BR' => 'Meu painel',
            'fr' => 'Mon tableau de bord',
            'de' => 'Mein Dashboard',
            'it' => 'Il mio cruscotto',
        ],
    ],
    'nav.employee_hours' => [
        'context' => 'navigation',
        'values' => [
            'en' => 'My hours',
            'es' => 'Mis horas',
            'pt_BR' => 'Minhas horas',
            'fr' => 'Mes heures',
            'de' => 'Meine Stunden',
            'it' => 'Le mie ore',
        ],
    ],
    'nav.employee_submit' => [
        'context' => 'navigation',
        'values' => [
            'en' => 'Submit week',
            'es' => 'Enviar semana',
            'pt_BR' => 'Enviar semana',
            'fr' => 'Soumettre la semaine',
            'de' => 'Woche einreichen',
            'it' => 'Invia settimana',
        ],
    ],
    'nav.employee_payslips' => [
        'context' => 'navigation',
        'values' => [
            'en' => 'Pay statements',
            'es' => 'Recibos de pago',
            'pt_BR' => 'Contracheques',
            'fr' => 'Bulletins de paie',
            'de' => 'Lohnabrechnungen',
            'it' => 'Cedolini',
        ],
    ],
    'nav.admin_header' => [
        'context' => 'navigation',
        'values' => [
            'en' => 'Admin',
            'es' => 'Administrador',
            'pt_BR' => 'Administrador',
            'fr' => 'Admin',
            'de' => 'Admin',
            'it' => 'Amministratore',
        ],
    ],
    'nav.admin_approvals' => [
        'context' => 'navigation',
        'values' => [
            'en' => 'Approvals',
            'es' => 'Aprobaciones',
            'pt_BR' => 'Aprovações',
            'fr' => 'Approbations',
            'de' => 'Genehmigungen',
            'it' => 'Approvazioni',
        ],
    ],
    'nav.admin_employees' => [
        'context' => 'navigation',
        'values' => [
            'en' => 'Employees',
            'es' => 'Empleados',
            'pt_BR' => 'Empregados',
            'fr' => 'Employés',
            'de' => 'Mitarbeitende',
            'it' => 'Dipendenti',
        ],
    ],
    'nav.admin_payruns' => [
        'context' => 'navigation',
        'values' => [
            'en' => 'Pay runs',
            'es' => 'Nóminas',
            'pt_BR' => 'Folhas de pagamento',
            'fr' => 'Cycles de paie',
            'de' => 'Lohnläufe',
            'it' => 'Cicli paga',
        ],
    ],
    'nav.admin_reports' => [
        'context' => 'navigation',
        'values' => [
            'en' => 'Reports & analytics',
            'es' => 'Reportes y análisis',
            'pt_BR' => 'Relatórios e análises',
            'fr' => 'Rapports et analyses',
            'de' => 'Berichte & Analysen',
            'it' => 'Report e analisi',
        ],
    ],
    'nav.admin_config' => [
        'context' => 'navigation',
        'values' => [
            'en' => 'Compliance config',
            'es' => 'Configuración de cumplimiento',
            'pt_BR' => 'Configuração de conformidade',
            'fr' => 'Configuration conformité',
            'de' => 'Compliance-Einstellungen',
            'it' => 'Configurazione conformità',
        ],
    ],
    'nav.admin_forms' => [
        'context' => 'navigation',
        'values' => [
            'en' => 'Forms & PDFs',
            'es' => 'Formularios y PDF',
            'pt_BR' => 'Formulários e PDFs',
            'fr' => 'Formulaires et PDF',
            'de' => 'Formulare & PDFs',
            'it' => 'Moduli e PDF',
        ],
    ],
    'nav.admin_audit' => [
        'context' => 'navigation',
        'values' => [
            'en' => 'Audit trail',
            'es' => 'Registro de auditoría',
            'pt_BR' => 'Trilha de auditoria',
            'fr' => 'Journal d’audit',
            'de' => 'Audit-Log',
            'it' => 'Registro di audit',
        ],
    ],
    'sidebar.tip_title' => [
        'context' => 'sidebar',
        'values' => [
            'en' => 'Need a quick win?',
            'es' => '¿Necesitas un avance rápido?',
            'pt_BR' => 'Precisa de um resultado rápido?',
            'fr' => 'Besoin d’un résultat rapide ?',
            'de' => 'Schneller Erfolg gefällig?',
            'it' => 'Serve un risultato rapido?',
        ],
    ],
    'sidebar.tip_body' => [
        'context' => 'sidebar',
        'values' => [
            'en' => 'Jump into approvals to keep payroll moving on schedule.',
            'es' => 'Ingresa a las aprobaciones para mantener la nómina a tiempo.',
            'pt_BR' => 'Abra as aprovações para manter a folha em dia.',
            'fr' => 'Passez aux approbations pour garder la paie à l’heure.',
            'de' => 'Wechseln Sie zu den Genehmigungen, um die Lohnabrechnung im Plan zu halten.',
            'it' => 'Vai alle approvazioni per mantenere le paghe puntuali.',
        ],
    ],
    'topbar.language_label' => [
        'context' => 'topbar',
        'values' => [
            'en' => 'Language',
            'es' => 'Idioma',
            'pt_BR' => 'Idioma',
            'fr' => 'Langue',
            'de' => 'Sprache',
            'it' => 'Lingua',
        ],
    ],
    'topbar.theme_toggle' => [
        'context' => 'topbar',
        'values' => [
            'en' => 'Theme',
            'es' => 'Tema',
            'pt_BR' => 'Tema',
            'fr' => 'Thème',
            'de' => 'Thema',
            'it' => 'Tema',
        ],
    ],
    'theme.system' => [
        'context' => 'theme',
        'values' => [
            'en' => 'System default',
            'es' => 'Según el sistema',
            'pt_BR' => 'Padrão do sistema',
            'fr' => 'Système',
            'de' => 'Systemstandard',
            'it' => 'Predefinito di sistema',
        ],
    ],
    'theme.light' => [
        'context' => 'theme',
        'values' => [
            'en' => 'Light',
            'es' => 'Claro',
            'pt_BR' => 'Claro',
            'fr' => 'Clair',
            'de' => 'Hell',
            'it' => 'Chiaro',
        ],
    ],
    'theme.dark' => [
        'context' => 'theme',
        'values' => [
            'en' => 'Dark',
            'es' => 'Oscuro',
            'pt_BR' => 'Escuro',
            'fr' => 'Sombre',
            'de' => 'Dunkel',
            'it' => 'Scuro',
        ],
    ],
    'dashboard.title' => [
        'context' => 'dashboard',
        'values' => [
            'en' => 'Overview',
            'es' => 'Resumen',
            'pt_BR' => 'Visão geral',
            'fr' => 'Aperçu',
            'de' => 'Überblick',
            'it' => 'Panoramica',
        ],
    ],
    'dashboard.subtitle' => [
        'context' => 'dashboard',
        'values' => [
            'en' => 'Track time, approvals, and payroll tasks in one place.',
            'es' => 'Gestiona horas, aprobaciones y nómina en un solo lugar.',
            'pt_BR' => 'Controle horas, aprovações e folha em um só lugar.',
            'fr' => 'Suivez heures, validations et paie au même endroit.',
            'de' => 'Verfolgen Sie Zeiten, Freigaben und Aufgaben der Lohnabrechnung an einem Ort.',
            'it' => 'Monitora ore, approvazioni e paghe in un’unica vista.',
        ],
    ],
    'dashboard.hero_title' => [
        'context' => 'dashboard',
        'values' => [
            'en' => 'Stay compliant with California household payroll rules.',
            'es' => 'Cumple con las reglas de nómina doméstica de California.',
            'pt_BR' => 'Fique em conformidade com as regras de folha doméstica da Califórnia.',
            'fr' => 'Restez conforme aux règles de paie domestique de Californie.',
            'de' => 'Bleiben Sie konform mit den kalifornischen Payroll-Regeln für Haushalte.',
            'it' => 'Rimani conforme alle regole retributive domestiche della California.',
        ],
    ],
    'dashboard.hero_body' => [
        'context' => 'dashboard',
        'values' => [
            'en' => 'Log hours, approve work, and generate wage statements with confidence in a mobile-first workspace.',
            'es' => 'Registra horas, aprueba jornadas y genera comprobantes con confianza desde el móvil.',
            'pt_BR' => 'Registre horas, aprove jornadas e gere holerites com confiança no celular.',
            'fr' => 'Enregistrez les heures, approuvez les journées et générez des bulletins en toute confiance sur mobile.',
            'de' => 'Erfassen Sie Stunden, genehmigen Sie Einsätze und erstellen Sie Lohnzettel mobil und sicher.',
            'it' => 'Registra ore, approva turni e genera cedolini in un’esperienza mobile-first.',
        ],
    ],
    'dashboard.cta_track_time' => [
        'context' => 'dashboard',
        'values' => [
            'en' => 'Log hours now',
            'es' => 'Registrar horas',
            'pt_BR' => 'Registrar horas',
            'fr' => 'Enregistrer des heures',
            'de' => 'Stunden erfassen',
            'it' => 'Registra ore',
        ],
    ],
    'dashboard.cta_view_reports' => [
        'context' => 'dashboard',
        'values' => [
            'en' => 'View compliance reports',
            'es' => 'Ver reportes de cumplimiento',
            'pt_BR' => 'Ver relatórios de conformidade',
            'fr' => 'Voir les rapports de conformité',
            'de' => 'Compliance-Berichte ansehen',
            'it' => 'Vedi report di conformità',
        ],
    ],
    'dashboard.quick_actions' => [
        'context' => 'dashboard',
        'values' => [
            'en' => 'Quick actions',
            'es' => 'Acciones rápidas',
            'pt_BR' => 'Ações rápidas',
            'fr' => 'Actions rapides',
            'de' => 'Schnelle Aktionen',
            'it' => 'Azioni rapide',
        ],
    ],
    'dashboard.guides_title' => [
        'context' => 'dashboard',
        'values' => [
            'en' => 'Helpful guides',
            'es' => 'Guías útiles',
            'pt_BR' => 'Guias úteis',
            'fr' => 'Guides pratiques',
            'de' => 'Hilfreiche Leitfäden',
            'it' => 'Guide utili',
        ],
    ],
    'dashboard.log_time' => [
        'context' => 'dashboard',
        'values' => [
            'en' => 'Enter a shift',
            'es' => 'Registrar un turno',
            'pt_BR' => 'Lançar turno',
            'fr' => 'Saisir un shift',
            'de' => 'Schicht erfassen',
            'it' => 'Inserisci un turno',
        ],
    ],
    'dashboard.submit_hours' => [
        'context' => 'dashboard',
        'values' => [
            'en' => 'Submit this week',
            'es' => 'Enviar esta semana',
            'pt_BR' => 'Enviar semana',
            'fr' => 'Soumettre la semaine',
            'de' => 'Diese Woche einreichen',
            'it' => 'Invia questa settimana',
        ],
    ],
    'dashboard.guide_how_to' => [
        'context' => 'dashboard',
        'values' => [
            'en' => 'How approvals work',
            'es' => 'Cómo funcionan las aprobaciones',
            'pt_BR' => 'Como funcionam as aprovações',
            'fr' => 'Fonctionnement des approbations',
            'de' => 'So funktionieren Genehmigungen',
            'it' => 'Come funzionano le approvazioni',
        ],
    ],
    'dashboard.guide_psl' => [
        'context' => 'dashboard',
        'values' => [
            'en' => 'Paid sick leave overview',
            'es' => 'Resumen de licencias pagadas',
            'pt_BR' => 'Visão geral de licença remunerada',
            'fr' => 'Aperçu des congés maladie payés',
            'de' => 'Überblick über den bezahlten Krankenstand',
            'it' => 'Panoramica delle assenze retribuite',
        ],
    ],
    'dashboard.keep_momentum' => [
        'context' => 'dashboard',
        'values' => [
            'en' => 'Keep momentum going',
            'es' => 'Sigue el impulso',
            'pt_BR' => 'Mantenha o ritmo',
            'fr' => 'Gardez le rythme',
            'de' => 'Bleiben Sie dran',
            'it' => 'Mantieni il ritmo',
        ],
    ],
    'locale.en' => [
        'context' => 'locale',
        'values' => [
            'en' => 'English',
            'es' => 'Inglés',
            'pt_BR' => 'Inglês',
            'fr' => 'Anglais',
            'de' => 'Englisch',
            'it' => 'Inglese',
        ],
    ],
    'locale.es' => [
        'context' => 'locale',
        'values' => [
            'en' => 'Spanish',
            'es' => 'Español',
            'pt_BR' => 'Espanhol',
            'fr' => 'Espagnol',
            'de' => 'Spanisch',
            'it' => 'Spagnolo',
        ],
    ],
    'locale.pt_BR' => [
        'context' => 'locale',
        'values' => [
            'en' => 'Português (Brasil)',
            'es' => 'Portugués (Brasil)',
            'pt_BR' => 'Português (Brasil)',
            'fr' => 'Portugais (Brésil)',
            'de' => 'Portugiesisch (Brasilien)',
            'it' => 'Portoghese (Brasile)',
        ],
    ],
    'locale.fr' => [
        'context' => 'locale',
        'values' => [
            'en' => 'Français',
            'es' => 'Francés',
            'pt_BR' => 'Francês',
            'fr' => 'Français',
            'de' => 'Französisch',
            'it' => 'Francese',
        ],
    ],
    'locale.de' => [
        'context' => 'locale',
        'values' => [
            'en' => 'Deutsch',
            'es' => 'Alemán',
            'pt_BR' => 'Alemão',
            'fr' => 'Allemand',
            'de' => 'Deutsch',
            'it' => 'Tedesco',
        ],
    ],
    'locale.it' => [
        'context' => 'locale',
        'values' => [
            'en' => 'Italiano',
            'es' => 'Italiano',
            'pt_BR' => 'Italiano',
            'fr' => 'Italien',
            'de' => 'Italienisch',
            'it' => 'Italiano',
        ],
    ],
];

foreach ($translations as $key => $payload) {
    $repository->upsert($key, $payload['context'], $payload['values']);
}

echo sprintf("Seeded %d i18n keys." . PHP_EOL, count($translations));
