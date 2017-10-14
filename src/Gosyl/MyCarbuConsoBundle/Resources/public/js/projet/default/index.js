/**
 * Created by alexandre on 19/07/17.
 */
if(typeof Gosyl === 'undefined') {
    Gosyl = {};
}

if(typeof Gosyl.MyCarbuConso === 'undefined') {
    Gosyl.MyCarbuConso = {};
}

if(typeof Gosyl.MyCarbuConso.Default === 'undefined') {
    Gosyl.MyCarbuConso.Default = {};
}

Gosyl.MyCarbuConso.Default.Index = (function($) {
    function init() {
        $('#divVehicules').on('click', '.fa-plus-square-o', addConso)
            .on('click', '.fa-line-chart', goToStat);
        $('#datePlein').datepicker();
    }

    function addBtnActionsVehicule(oVehicule) {
        var idModeleUser = oVehicule.idModeleUser,
            contenu = '';

        contenu += '<div class="container-fluid">'+
                '<div class="row" data-vl=\''+JSON.stringify(oVehicule)+'\'>'+
                    '<div class="col-xs-6" style="text-align: center">'+
                        '<i id="btnAddConso_' + idModeleUser + '" class="fa fa-plus-square-o" style="font-size: 1.5em;  cursor: pointer;" title="Ajouter une consommation"></i>'+
                    '</div>'+
                    '<div class="col-xs-6" style="text-align: center">'+
                        '<i class="fa fa-line-chart" style="font-size: 1.5em; cursor: pointer;" title="Statistiques" id="btnOpenStat_' + idModeleUser + '"></i>'+
                    '</div>'+
                '</div>'+
            '</div>';

        return contenu;
    }

    function addConso(e) {
        e.stopPropagation();

        $('#modeleUser').val(Gosyl.Common.getIdItem($(this).attr('id')));
        $('#quantite').val('');
        $('#prix').val('');
        $('#distance').val('');
        $('#datePlein').val('');
        $('#commentaire').val('');

        $('#modalAjoutConso').modal('show')
            .find('#myModalLabel').html('Ajouter une consommation');
    }

    function goToStat(e) {
        e.stopPropagation();

        var idVehicule = Gosyl.Common.getIdItem($(this).attr('id'));

        document.location = Gosyl.Common.rootPath + 'stat/' + idVehicule;
    }

    return {
        init: init,
        addBtnActionsVehicule: addBtnActionsVehicule
    };
})(jQuery);

$(function() {
    Gosyl.MyCarbuConso.Default.Index.init();
});