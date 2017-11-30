<?php if(!defined('GIPISITE'))exit();
/*
* Observatoire des taux
*
* Copyright Thibault et Gilbert Mondary, Laboratoire de Recherche pour le Développement Local (2006--)
*
* labo@gipilab.org
*
* Ce logiciel est un programme informatique servant à visualiser différents indicateurs sur les taux
* (historique, courbes des taux, pression conjoncturelle...)
*
*
* Ce logiciel est régi par la licence CeCILL soumise au droit français et
* respectant les principes de diffusion des logiciels libres. Vous pouvez
* utiliser, modifier et/ou redistribuer ce programme sous les conditions
* de la licence CeCILL telle que diffusée par le CEA, le CNRS et l'INRIA
* sur le site "http://www.cecill.info".
*
* En contrepartie de l'accessibilité au code source et des droits de copie,
* de modification et de redistribution accordés par cette licence, il n'est
* offert aux utilisateurs qu'une garantie limitée. Pour les mêmes raisons,
* seule une responsabilité restreinte pèse sur l'auteur du programme, le
* titulaire des droits patrimoniaux et les concédants successifs.
*
* A cet égard l'attention de l'utilisateur est attirée sur les risques
* associés au chargement, à l'utilisation, à la modification et/ou au
* développement et à la reproduction du logiciel par l'utilisateur étant
* donné sa spécificité de logiciel libre, qui peut le rendre complexe à
* manipuler et qui le réserve donc à des développeurs et des professionnels
* avertis possédant des connaissances informatiques approfondies. Les
* utilisateurs sont donc invités à charger et tester l'adéquation du
* logiciel à leurs besoins dans des conditions permettant d'assurer la
* sécurité de leurs systèmes et ou de leurs données et, plus généralement,
* à l'utiliser et l'exploiter dans les mêmes conditions de sécurité.
*
* Le fait que vous puissiez accéder à cet en-tête signifie que vous avez
* pris connaissance de la licence CeCILL, et que vous en avez accepté les
* termes.
*
*/
?>
  <div class="row">
    <div class="col s12">
      <div class="center-align">
	<img class="responsive-img" src="logo_obsdestaux.png" alt="logo">
	<p class="flow-text">Laboratoire de Recherche pour le Développement Local</p>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col l6 s12">
      <h3>Présentation</h3>
      <p>L'observatoire des taux se propose de guider la réflexion des décideurs politiques et administratifs vers les choix les mieux adaptés à leurs besoins, dans les domaines sensibles de la maîtrise du coût de la dette et de la gestion des risques environnant un encours.</p>
      <p><strong>A travers cet outil, le Laboratoire de Recherche pour le Développement Local prend ouvertement position en faveur de l'indice EURIBOR pour l'adossement des emprunts à souscrire par les Collectivités locales.</strong></p>
      <p>Les supports d'adossement du type TEC pour les emprunts dits «&nbsp;à taux fixe&nbsp;» sont déconseillés par le Laboratoire du fait des risques majeurs que ces emprunts font peser sur les Collectivités emprunteuses et dont il est essentiel de prendre la mesure. Ces risques sont qualifiés d'indirects par le Laboratoire car une apparente innocuité des taux fixes est entretenue par les argumentaires de vente.</p>
      <p>Cette position est renforcée par la théorie dénommée «&nbsp;<b>GFM©</b>&nbsp;» présentée par le Laboratoire de Recherche pour le Développement Local sous la forme d'une démonstration graphique du risque de surcoût qu'induit la souscription d'un emprunt à taux fixe sur référence TEC ou tout autre indice.</p>
    </div>
    <div class="col l6 s12">
      <h4>Ironie du verbe ?</h4>
      <p>Pour une Collectivité territoriale, l'unique justification d'un taux fixe, soit la garantie de stabilité face à une remontée (en cours ou hypothétique) des taux indexés, est largement débordée lorsque la conjoncture vient à tirer les taux indexés vers le bas ! Le taux fixe joue alors, en effet, un rôle inverse à celui pour lequel il a été choisi : blocage des décisions à cause de l'impact financier de l'indemnité actuarielle, surcoût des échéances, critiques du poids budgétaire de la dette...<br>
      Or, il s'avère qu'il est dans la nature même des taux indexés de monter, de descendre, puis de monter à nouveau, et ainsi de suite.</p>
      <p><strong>Il s'agit d'une oscillation, liée à divers paramètres politiques, économiques et financiers, nationaux et internationaux.</strong></p>
      <p>Les marchés inspirent et expirent. Lorsqu'ils sont bas, il advient nécessairement que les taux indexés remontent, simplement pour qu'ils puissent redescendre ensuite, dès que possible (ou que nécessaire). L'histoire démontre également que la période de cette oscillation est toujours inférieure de plusieurs grandeurs à la durée (15 ans, parfois plus) des emprunts souscrits par les Collectivités territoriales.</p>
      <p><strong>Il est donc primordial que les décideurs locaux disposent, avec l'Observatoire des taux, des éléments d'analyses qui leur permettent de prendre en compte cet état de fait.</strong></p>
    </div>
  </div>
  <div class="row">
    <div class="col s12">
      <h3>Glossaire</h3>
      <dl>
	<dt>emprunt</dt>
	<dd>valeur obtenue d'un tiers (le prêteur), sous diverses conditions. L'emprunt compense le plus souvent un déséquilibre financier constaté par l'emprunteur.</dd>
	<dt>prêt</dt>
	<dd>valeur, le plus souvent financière, fournie à un tiers (l'emprunteur) sous condition expresse de restitution</dd>

	<dt>Euribor</dt>
	<dd>(Euro Interbank Offered Rate) est l'indice de référence du marché monétaire de la zone euro depuis le 1er janvier 1999</dd>

	<dt>Eonia</dt>
	<dd>(Euro OverNight Index Average) est le taux de référence quotidien des dépôts interbancaires en blanc (c'est-à-dire sans être gagés par des titres) effectués au jour-le-jour dans la zone euro</dd>
	
<dt>TEC</dt>
	<dd>(Taux de l'Échéance Constante) est l'indice déterminé par "fixage" par le Comité de Normalisation Obligataire (CNO). Il est calculé pour des maturités de 1, 2, 3, 5, 7, 10, 15, 20, 25 et 30 ans</dd>

	<dt>Maturité</dt>
	<dd>se dit du terme sur lequel est calculé le rendement d'un indice. Les maturités présentées dans l'observatoire des taux sont Eonia, Euribor 1 mois, 3 mois, 6 mois et 12 mois pour les taux variables, TEC10, 15, 20, 25 et 30 ans pour les taux fixes.</dd>

	<dt>Rendement</dt>
	<dd>se dit de la cotation d'un indice pour telle maturité. Le rendement de l'EURIBOR 1 mois le 17 janvier 2007 est de 3,615%</dd>

	<dt>Point de base</dt>
	<dd>1 point de base correspond à 1 centième pour cent ou 0,01%</dd>

	<dt>Courbe des taux</dt>
	<dd>une courbe des taux trace le profil des rendements d'un indice pour chaque maturité à une date donnée. Pour être exploitable, une courbe des taux doit être comparée à son homologue à une date ultérieure. Seules les modifications de profil d'une date à l'autre peuvent éclairer l'analyste.</dd>
	<dt>Courbe d'évolution d'un taux</dt>
	<dd>une courbe d'évolution des taux trace le profil du rendement d'une maturité pour une période. Contrairement à une courbe des taux, la courbe d'évolution d'un taux se suffit à elle-même en terme d'analyse.</dd>

	<dt>indemnité actuarielle</dt>
	<dd>se dit de la somme à verser au prêteur en contrepartie du remboursement anticipé d'un emprunt à taux fixe. L'indemnité actuarielle correspond à la somme des intérêts à échoir entre la date de remboursement anticipé et la date d'échéance normale de l'emprunt.</dd>
	<dt>risque de taux</dt>
	<dd>le risque de taux est le déterminant de la symétrie prêteur - emprunteur. Pour le prêteur, il mesure, entre autres, le manque à gagner induit par un prêt livré en période de hausse des taux. Pour un emprunteur, il mesure , entre autre, le surcoût induit par une souscription tardive en période de hausse des taux ou prématurée en période de baisse des taux.</dd>
      </dl>
    </div>
  </div>

