/**
 * Created by alexandre on 07/07/17.
 */
if(typeof Gosyl === 'undefined') {
    Gosyl = {};
}

if(typeof Gosyl.MyCarbuConso === 'undefined') {
    Gosyl.MyCarbuConso = {};
}

if(typeof Gosyl.MyCarbuConso.Utilisateurs === 'undefined') {
    Gosyl.MyCarbuConso.Utilisateurs = {};
}

Gosyl.MyCarbuConso.Utilisateurs.Profil = (function() {
    function init() {
        $('#btnAddVehicule').on('click', afficheFormAjoutVehicule);

        $('#modalAjoutVehicule').on('hidden.bs.modal', razFormAjoutVehicule);

        $('#typeMine').on('keyup', function(e) {
            e.stopPropagation();
            $('#btnRechercheVehicule').prop('disabled', $('#typeMine').val().length === 0);
        });

        $('#btnNewRecherche').on('click', razFormAjoutVehicule);

        $('#btnRechercheVehicule').on('click', launchSearchVehicule);

        $('#btnModifRecherche').on('click', modifRecherche);

        $('#btnLaunchSupprVl').on('click', launchSuppressionVehicule);

        $('#marque').val('').autocomplete({
            appendTo: "#formAjoutVehicule",
            source: function (request, response) {
                $.ajax({
                    type: 'post',
                    cache: false,
                    url: Gosyl.Common.rootPath + 'carbu/ajax/getmarque',
                    data: {
                        q: request.term.toUpperCase()
                    },
                    dataType: 'json'
                }).then(function(data) {
                    response($.map(data, function(item) {
                        var id = item.id,
                            libelle = item.libelle;

                        return {
                            label: libelle,
                            value: libelle,
                            id: id,
                            libelle: libelle
                        }
                    }));
                });
            },
            open: function () {
                $('div.ui-helper-hidden-accessible').hide();
            },
            delai: 800,
            minLength: 2,
            select: function(e, ui) {
                e.stopPropagation();
                $('#hiddenIdMarque').val(ui.item.id);
                $('#marque').val(ui.item.libelle);

                activateNomCommercial(ui.item.id);
            }
        });

        var $divVehicules = $('#divVehicules');

        $divVehicules.on('click', 'i.fa-pencil',  editVehicule);

        $divVehicules.on('click', 'i.fa-trash', suppressionVehicule);
    }

    /*function activateAnnee(e, bForModif, oVehicule) {
        if(typeof bForModif === 'undefined') {
            bForModif = false;
            oVehicule = {};
        }

        e.stopPropagation();
        var $anneeMiseEnCirculation = $('#anneeMiseEnCirculation'),
            $puissanceFiscale = $('#puissanceFiscale');

        $puissanceFiscale.prop('disabled', true);
        $anneeMiseEnCirculation.prop('disabled', false)
            .on('keypress', activateBtnRechercher);


    }*/

    function activateBoiteVitesse(e, bForModif, oVehicule) {
        if(typeof bForModif === 'undefined') {
            bForModif = false;
            oVehicule = {};
        }

        e.stopPropagation();
        var $nomCommercial = $('#nomCommercial'),
            $energie = $('#energie'),
            $boiteVitesse = $('#boiteVitesse');

        $energie.prop('disabled', true);
        $boiteVitesse.focus();

        $.ajax({
                type: 'post',
                cache: false,
                url: Gosyl.Common.rootPath + 'carbu/ajax/getboitevitesse',
                data: {
                    idNomCommercial: $nomCommercial.val(),
                    idEnergie: $energie.val()
                },
                dataType: 'json'
            }).then(function(data) {
                $.each(data, function(i, boite) {
                    $boiteVitesse.append('<option value="' + boite.id + '">' + boite.libelle + '</option>')
                });

            if(bForModif) {
                $boiteVitesse.find('option[value=' + oVehicule.idBoite + ']').prop('selected', true);
                $boiteVitesse.trigger('change', [true, oVehicule]);
            }
            });
            $boiteVitesse.prop('disabled', false)
                .on('change', activateCarrosserie);
    }

    function activateCarrosserie(e, bForModif, oVehicule) {
        if(typeof bForModif === 'undefined') {
            bForModif = false;
            oVehicule = {};
        }

        e.stopPropagation();
        var $nomCommercial = $('#nomCommercial'),
            $energie = $('#energie'),
            $boiteVitesse = $('#boiteVitesse'),
            $carrosserie = $('#carrosserie');

        $boiteVitesse.prop('disabled', true);
        $carrosserie.focus();

        $.ajax({
                type: 'post',
                cache: false,
                url: Gosyl.Common.rootPath + 'carbu/ajax/getcarrosserie',
                data: {
                    idNomCommercial: $nomCommercial.val(),
                    idEnergie: $energie.val(),
                    idBoiteVitesse: $boiteVitesse.val()
                },
                dataType: 'json'
        }).then(function(data) {
            $.each(data, function(i, carrosserie) {
                $carrosserie.append('<option value="' + carrosserie.id + '">' + carrosserie.libelle + '</option>')
            });

            if(bForModif) {
                $carrosserie.find('option[value=' + oVehicule.idCarrosserie + ']').prop('selected', true);
                $carrosserie.trigger('change', [true, oVehicule]);
            }
        });

        $carrosserie.prop('disabled', false)
            .on('change', activatePuissance);
    }

    function activateEnergie(e, bForModif, oVehicule) {
        if(typeof bForModif === 'undefined') {
            bForModif = false;
            oVehicule = {};
        }

        e.stopPropagation();
        var $nomCommercial = $('#nomCommercial'),
            $energie = $('#energie');

        $nomCommercial.prop('disabled', true);
        $energie.focus();

        $.ajax({
                type: 'post',
                cache: false,
                url: Gosyl.Common.rootPath + 'carbu/ajax/getenergie',
                data: {
                    idNomCommercial: $nomCommercial.val()
                },
                dataType: 'json'
            }).then(function(data) {
                $.each(data, function(i, energie) {
                    $energie.append('<option value="' + energie.id + '">' + energie.libelle + '</option>')
                });

                if(bForModif) {
                    $energie.find('option[value='+oVehicule.idEnergie+']').prop('selected', true);
                    $energie.trigger('change', [true, oVehicule])
                }
            });
            $energie.prop('disabled', false)
                .on('change', activateBoiteVitesse);
    }

    function activateNomCommercial(idMarque, bForModif, oVehicule) {
        if(typeof bForModif === 'undefined') {
            bForModif = false;
            oVehicule = {};
        }

        // recherche ajax des noms commerciaux
        var $nomCommercial = $('#nomCommercial'),
            $marque = $('#marque');
        $nomCommercial.html('<option selected>Veuillez faire un choix</option>').focus();

        $marque.prop('disabled', true);

        $.ajax({
            type: 'post',
            cache: false,
            url: Gosyl.Common.rootPath + 'carbu/ajax/getnomcommercial',
            data: {
                q: idMarque
            },
            dataType: 'json'
        }).then(function(data) {

            $.each(data, function(i, nom) {
                $nomCommercial.append('<option value="' + nom.id + '">' + nom.libelle + '</option>');
            });
            $nomCommercial.prop('disabled', false)
                .on('change', activateEnergie);

            if(bForModif) {
                $nomCommercial.find('option[value='+oVehicule.idNomCommercial+']').prop('selected', true);
                $nomCommercial.trigger('change', [true, oVehicule]);
            }
        });
    }

    function activatePuissance(e, bForModif, oVehicule) {
        if(typeof bForModif === 'undefined') {
            bForModif = false;
            oVehicule = {};
        }

        e.stopPropagation();
        var $nomCommercial = $('#nomCommercial'),
            $energie = $('#energie'),
            $boiteVitesse = $('#boiteVitesse'),
            $carrosserie = $('#carrosserie'),
            $puissanceFiscale = $('#puissanceFiscale');

        $carrosserie.prop('disabled', true);
        $puissanceFiscale.focus();

        $.ajax({
                type: 'post',
                cache: false,
                url: Gosyl.Common.rootPath + 'carbu/ajax/getpuissancefiscale',
                data: {
                    idNomCommercial: $nomCommercial.val(),
                    idEnergie: $energie.val(),
                    idBoiteVitesse: $boiteVitesse.val(),
                    idCarrosserie: $carrosserie.val()
                },
                dataType: 'json'
            }).then(function(data) {
                $.each(data, function(i, puissance) {
                    $puissanceFiscale.append('<option value="' + puissance.id + '">' + puissance.libelle + '</option>')
                });

                if(bForModif) {
                    $puissanceFiscale.find('option[value=' + oVehicule.idPuissance + ']').prop('selected', true);
                    $puissanceFiscale.trigger('change');
                }

            });
            $puissanceFiscale.prop('disabled', false)
                .on('change', activateBtnRechercher);


    }

    function activateBtnRechercher(e)  {
        e.stopPropagation();
        var $btnRechercheVehicule = $('#btnRechercheVehicule');

        $('#puissanceFiscale').prop('disabled', true);

        $btnRechercheVehicule.prop('disabled', false);

    }

    function addBtnActionsVehicule(oVehicule) {
        var idModeleUser = oVehicule.idModeleUser,
            contenu = '';

        contenu += '<div class="container-fluid">'+
            '<div class="row" data-vl=\''+JSON.stringify(oVehicule)+'\'>'+
                '<div class="col-xs-6" style="text-align: center">'+
                    '<i class="fa fa-pencil" style="font-size: 1.5em; cursor: pointer;" title="Éditer ce véhicule" id="btnEditVl_' + idModeleUser + '"></i>'+
                '</div>'+
                '<div class="col-xs-6" style="text-align: center">'+
                    '<i id="btnSuppressionVl_' + idModeleUser + '" class="fa fa-trash" style="font-size: 1.5em;  cursor: pointer;" title="Supprimer ce véhicule"></i>'+
                '</div>'+
            '</div>'+
        '</div>';

        return contenu;
    }

    function afficheFormAjoutVehicule(e) {
        e.stopPropagation();
        razFormAjoutVehicule(e);
        $('#modalAjoutVehicule').modal('show');
    }

    function editVehicule(e) {
        e.stopPropagation();
        var $this = $(this),
            id = Gosyl.Common.getIdItem($this.attr('id')),
            oVehicule = $this.parent().parent().data('vl'),
            $modalAjoutVehicule = $('#modalAjoutVehicule');

        // Remplissage du formulaire
        $('#idModeleUser').val(oVehicule.idModeleUser);
        $('#nomVehicule').val(oVehicule.nomVehicule);

        $('#marque').val(oVehicule.libelleMarque);
        $('#hiddenIdMarque').val(oVehicule.idMarque);
        $('#kilometrageInit').val(oVehicule.kilometrageInit);

        $('#anneeMiseEnCirculation').val(oVehicule.anneeMiseEnCirculation);

        // Affichage de la modale
        $modalAjoutVehicule.find('#myModalLabel').html('Modifier un véhicule');
        $modalAjoutVehicule.modal('show');

        activateNomCommercial(oVehicule.idMarque, true, oVehicule);
    }

    function launchSearchVehicule(e) {
        e.stopPropagation();

        var $marque = $('#marque'),
            $hiddenIdMarque = $('#hiddenIdMarque'),
            $nomCommercial = $('#nomCommercial'),
            $energie = $('#energie'),
            $boiteVitesse = $('#boiteVitesse'),
            $carrosserie = $('#carrosserie'),
            $puissanceFiscale = $('#puissanceFiscale'),
            $anneeMiseEnCirculation = $('#anneeMiseEnCirculation'),
            $typeMine = $('#typeMine'),
            data = {};

        if($anneeMiseEnCirculation.val() === '') {
            $anneeMiseEnCirculation.focus();
            return false;
        }

        if($marque.val() !== '' && testSelect($nomCommercial) && testSelect($energie) && testSelect($boiteVitesse) && testSelect($carrosserie) && testSelect($puissanceFiscale) && $anneeMiseEnCirculation.val() !== '' ) {
            data = {
                marque: $hiddenIdMarque.val(),
                nomCommercial: $nomCommercial.find('option:selected').val(),
                energie: $energie.find('option:selected').val(),
                boiteVitesse: $boiteVitesse.find('option:selected').val(),
                carrosserie: $carrosserie.find('option:selected').val(),
                puissanceFiscale: $puissanceFiscale.find('option:selected').val(),
                anneeMiseEnCirculation: $anneeMiseEnCirculation.val()
            };
        } else if($typeMine.val() !== '') {
            data = {
                typeMine: $typeMine.val().toUpperCase()
            };
        }



        $.ajax({
            type: 'post',
            cache: false,
            url: Gosyl.Common.rootPath + 'carbu/ajax/getmodeles',
            data: data,
            dataType: 'json'
        }).then(function(json) {
            var $resultRechAjoutVl = $('#resultRechAjoutVl'),
                $tbody = $resultRechAjoutVl.find('tbody');

            // RAZ du tableau de résultat de la recherche
            $tbody.html('');

            if($.fn.DataTable.isDataTable($resultRechAjoutVl)) {
                $resultRechAjoutVl.DataTable().destroy();
            }

            $.each(json, function(i, modele) {
                $tbody.append('<tr data-id="' + modele.id + '"><td>' + modele.libelleNomCommercial + '</td><td>' + modele.libelle + '</td><td>'+ modele.anneeModele + '</td><td>' + modele.typeMine + '</td></tr>')
            });

            $('#frmRecherche').hide();
            $('#resultRech').show();

            $resultRechAjoutVl.dataTable({
                autoWidth: true,
                info: false,
                paging: false,
                searching: false,
                createdRow: function(row) {
                    var $row = $(row),
                        $nomVehicule = $('#nomVehicule'),
                        $anneeMiseEnCirculation = $('#anneeMiseEnCirculation');
                    $row.on('click', function(e) {
                        e.stopPropagation();
                        // Ajout du modèle

                        var idModele = $row.data('id');
                        $('#modele').val(idModele);

                        if(!$nomVehicule.get(0).checkValidity()) {
                            $nomVehicule.focus();
                            return;
                        }

                        if(!$anneeMiseEnCirculation.get(0).checkValidity()) {
                            $anneeMiseEnCirculation.focus();
                            return;
                        }

                        $('#formAjoutVehicule').get(0).submit();
                    })
                        .css('cursor', 'pointer')
                        .attr('title', 'Ajouter ce véhicule');
                }
            });

        }).fail(function(json) {
            console.log(json);
        });
    }

    function launchSuppressionVehicule(e) {
        e.stopPropagation();

        oVehicule = $('#modalSuppressionVehicule').data('vehicule');

        document.location = Gosyl.Common.rootPath + 'carbu/gestionbasevehicule/supprvl/' + oVehicule.idModeleUser;
    }

    function modifRecherche(e) {
        e.stopPropagation();

        $('#resultRech').hide();
        razFormAjoutVehicule(e);
        $('#frmRecherche').show();
    }

    function suppressionVehicule(e) {
        var $this = $(this);

        e.stopPropagation();

        var id = Gosyl.Common.getIdItem($this.attr('id')),
            $dataVl = $('#data_' + id);

        $('#spanNomVehicule').html($dataVl.data('vehicule').nomVehicule);

        $('#modalSuppressionVehicule').modal('show').data('vehicule', $dataVl.data('vehicule'));
    }

    function testSelect(select) {
        return select.find('option:selected').val() !== 'Veuillez faire un choix';
    }

    function razFormAjoutVehicule(e) {
        e.stopPropagation();
        var $resultRechAjoutVl = $('#resultRechAjoutVl'),
            $modalAjoutVehicule = $('#modalAjoutVehicule');

        $modalAjoutVehicule.find('#myModalLabel').html('Ajouter un véhicule');

        $('#frmRecherche').show();
        $('#resultRech').hide();
        $resultRechAjoutVl.find('tbody').html('');

        if($.fn.DataTable.isDataTable($resultRechAjoutVl)) {
            $resultRechAjoutVl.DataTable().destroy();
        }

        $('#nomVehicule').val('');
        $('#kilometrageInit').val('');

        $('#anneeMiseEnCirculation').val('');

        var $marque = $('#marque'),
            $nomCommercial = $('#nomCommercial'),
            $energie = $('#energie'),
            $boiteVitesse = $('#boiteVitesse'),
            $carrosserie = $('#carrosserie'),
            $puissanceFiscale = $('#puissanceFiscale'),
            $anneeModele = $('#anneeModele'),
            $btnRechercheVehicule = $('#btnRechercheVehicule');




        // RAZ de marque
        $marque.prop('disabled', false)
            .val('');

        // RAZ des différents select
        RAZSelect($nomCommercial);
        RAZSelect($energie);
        RAZSelect($boiteVitesse);
        RAZSelect($carrosserie);
        RAZSelect($puissanceFiscale);
        RAZSelect($anneeModele);

        $anneeModele.prop('disabled', true)
            .val('');

        $btnRechercheVehicule.prop('disabled', true);
    }

    function RAZSelect(select) {
        select.prop('disabled', true)
            .html('<option value="" selected >Veuillez faire un choix</option>')
            .off('change');
    }

    return {
        init: init,
        addBtnActionsVehicule: addBtnActionsVehicule
    };
})();

$(document).ready(function () {
    Gosyl.MyCarbuConso.Utilisateurs.Profil.init();

});