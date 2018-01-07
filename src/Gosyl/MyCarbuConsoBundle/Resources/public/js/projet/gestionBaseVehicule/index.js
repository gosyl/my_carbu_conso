/**
 * Created by alexandre on 07/07/17.
 */
if(typeof Gosyl === 'undefined') {
    Gosyl = {};
}

if(typeof Gosyl.MyCarbuConso === 'undefined') {
    Gosyl.MyCarbuConso = {};
}

if(typeof Gosyl.MyCarbuConso.GestionBaseVehicules === 'undefined') {
    Gosyl.MyCarbuConso.GestionBaseVehicules = {};
}

Gosyl.MyCarbuConso.GestionBaseVehicules.Index = (function() {
    function init() {
        $('#btnRefresh').on('click', launchRefresh);
    }

    function launchRefresh(e) {
        e.stopPropagation();
        document.location = Gosyl.MyCarbuConso.rootPath + 'carbu/gestionbasevehicule/refresh';
    }

    return {
        init: init
    };
})();

$(document).ready(function () {
    Gosyl.MyCarbuConso.GestionBaseVehicules.Index.init();
});