package org.gipilab.observatoiredestaux;

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

interface UrlTable {
    String BASEURL = "https://gipilab.org/pourAppliObservatoire/";
    String URLCOURBESTAUXVARIABLES=BASEURL+"index.php?page=1";
    String URLCOURBESTAUXFIXES=BASEURL+"index.php?page=2";
    String URLHISTORIQUETAUXVARIABLES=BASEURL+"index.php?page=3";
    String URLHISTORIQUETAUXFIXES=BASEURL+"index.php?page=4";
    String URLPRESSIONCONJONCTURELLETAUXVARIABLES=BASEURL+"index.php?page=5";
    String URLPRESSIONCONJONCTURELLETAUXFIXES=BASEURL+"index.php?page=6";
    String URLJAUGES=BASEURL+"index.php?page=7";
    String URLVOLATILITETAUXVARIABLES=BASEURL+"index.php?page=8";
    String URLVOLATILITETAUXFIXES=BASEURL+"index.php?page=9";
    String URLTHEORIEGFM=BASEURL+"index.php?page=11";
    String URLPRESENTATION=BASEURL+"index.php?page=12";
}
