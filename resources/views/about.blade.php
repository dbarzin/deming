@extends("layout")

@section("content")
<div class="p-3">
    <div data-role="panel" data-title-caption="About" data-collapsible="true" data-title-icon="<span class='mif-help2'></span>">
		<div class="grid">
	    	<div class="row">
	    		<div class="cell-4 text-center">
                    <img src="/images/deming.png" width=400>
                </div>
	    		<div class="cell">
@if(Auth::User()->language==="fr")
    <h1>À propos de Deming</h1>

    <p><strong>Deming</strong> est un outil open-source conçue pour aider les organisations à gérer, surveiller et améliorer leurs processus de sécurité de l'information. Développée avec l'objectif de s'aligner sur la norme ISO/IEC 27001:2013, <strong>Deming</strong> offre une solution complète aux organisations qui cherchent à maintenir des mesures de sécurité efficaces et proportionnées.</p>

    <h2>Pourquoi Deming ?</h2>
    <p>Dans l'environnement actuel, il est essentiel de s'assurer que vos mesures de sécurité ne sont pas seulement mises en œuvre, mais également surveillées en permanence. <strong>Deming</strong> simplifie le processus de suivi, de planification et de reporting sur l'efficacité de vos contrôles de sécurité, aidant ainsi votre organisation à répondre aux exigences strictes de conformité tout en optimisant la gestion de la sécurité.</p>

    <h2>Open Source et communautaire</h2>
    <p>En tant qu'initiative open-source, <strong>Deming</strong> invite la communauté à contribuer, assurant ainsi que la plateforme continue d'évoluer avec les technologies et les meilleures pratiques de gestion de la sécurité. Le projet est distribué sous la licence <a href="https://www.gnu.org/licenses/licenses.html">GPL</a>, et nous accueillons avec plaisir les développeurs, professionnels de la sécurité et organisations souhaitant participer à son développement et à son amélioration.</p>

    <p>Pour plus de détails sur la manière de contribuer ou d'utiliser l'application, consultez la <a href="https://dbarzin.github.io/deming/index.fr/">documentation utilisateur</a> et explorez notre <a href="https://github.com/dbarzin/deming">Github</a> pour découvrir les fonctionnalités à venir.</p>

@else
    <h1><strong>About Deming</strong></h1>

    <p><strong>Deming</strong> is an open-source project designed to empower organizations in managing, monitoring, and improving their information security processes. Built with a focus on aligning with the ISO/IEC 27001:2013 standard, <strong>Deming</strong> offers a comprehensive solution for organizations striving to maintain effective and proportionate security measures.</p>

    <h2>Why Deming?</h2>
    <p>In today’s environment, ensuring that your security measures are not only implemented but continuously monitored is crucial. <strong>Deming</strong> simplifies the process of tracking, planning, and reporting on the effectiveness of your security controls, helping your organization meet strict compliance requirements while optimizing your security management.</p>

    <h2>Open Source and Community-Driven</h2>
    <p>As an open-source initiative, <strong>Deming</strong> invites contributions from the community, ensuring that the platform continues to evolve with the latest technologies and best practices in security management. The project is licensed under the <a href="https://www.gnu.org/licenses/licenses.html">GPL</a>, and we welcome developers, security professionals, and organizations to participate in its development and improvement.</p>

    <p>For more details on how to contribute or use the application, visit the <a href="https://dbarzin.github.io/deming">user documentation</a> and explore our <a href="https://github.com/dbarzin/deming">Github</a> for upcoming features.</p>
@endif
                </div>
	    		<div class="cell-1">
                </div>
            </div>
        </div>
    </div>
</div>

 @endsection
