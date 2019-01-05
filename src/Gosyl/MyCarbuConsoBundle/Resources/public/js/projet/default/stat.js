if(typeof Gosyl === 'undefined') {
    Gosyl = {};
}

if(typeof Gosyl.MyCarbuConso === 'undefined') {
    Gosyl.MyCarbuConso = {};
}

if(typeof Gosyl.MyCarbuConso.Default === 'undefined') {
    Gosyl.MyCarbuConso.Default = {};
}

Gosyl.MyCarbuConso.Default.Stat = (function($) {
    var map = {};
    var points = {};
    var dataConso;

    //var timeLocaleOpened = false;

    var idModeleUser = 0;
    var dataEvo = {
        prix: [],
        conso: []
    };

    var txtLegend = {
        prix: "Prix au litre (€/l)",
        conso: "Nbre de litre consommés (l/100km)"
    };

    var uniteTooltip = {
        prix: "€/l",
        conso: "l/100km"
    };

    function init() {
        var $divConso = $('#divConso');

        $divConso.on('click', 'i.fa-pencil',  editConso)
            .on('click', 'i.fa-trash', suppressionConso);

        $('#btnAddConso').on('click', addConso);

        $('#datePlein').datepicker();

        d3.json("https://unpkg.com/d3-time-format@2/locale/fr-FR.json", function(error, locale) {
            if (error) throw error;
            d3.timeFormatDefaultLocale(locale);
        });

        $('#dateDebut').datepicker({
            minDate: new Date(2017, 1 - 1, 1),
            maxDate: new Date()
        });

        $('#dateFin').datepicker({
            minDate: new Date(2017, 1 - 1, 1),
            maxDate: new Date()
        });

        $('#btnValiderChoixDate').on('click', function(e) {
            e.stopPropagation();

            $('#formChoixDate').get(0).submit();
        });

        $('#btnRAZDate').on('click', function(e) {
            e.stopPropagation();

            $('#dateDebut, #dateFin').val('');
            $('#formChoixDate').get(0).submit();
        });

        $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
            e.stopPropagation();
            var $target = $(e.target);

            if($target.attr('aria-controls') === 'carto') {
                resizeGMaps();
            }
        });

        $(window).on('resize', function(e) {
            e.stopPropagation();

            resizeGMaps();
        });
    }

    function resizeGMaps() {
        if(typeof Gosyl.MyCarbuConso.Default.Stat.map === 'object') {
            var center = Gosyl.MyCarbuConso.Default.Stat.map.getCenter();
            google.maps.event.trigger(Gosyl.MyCarbuConso.Default.Stat.map, 'resize');
            Gosyl.MyCarbuConso.Default.Stat.map.setCenter(center);
        }
    }

    function addConso(e) {
        e.stopPropagation();

        $('#modeleUser').val(Gosyl.MyCarbuConso.Default.Stat.idModeleUser);
        $('#quantite').val('');
        $('#prix').val('');
        $('#distance').val('');
        $('#datePlein').val('');
        $('#commentaire').val('');

        $('#modalAjoutConso').modal('show')
            .find('#myModalLabel').html('Ajouter une consommation');
    }

    function editConso(e) {
        e.stopPropagation();

        var oConso = $('tr[data-idconso="'+$(this).data('id')+'"]').data('conso');

        $('#modeleUser').val(Gosyl.MyCarbuConso.Default.Stat.idModeleUser);
        $('#quantite').val(oConso.quantite);
        $('#prix').val(oConso.prix);
        $('#distance').val(oConso.distance);
        $('#datePlein').val(oConso.datePlein);
        $('#kilometrageCompteur').val(oConso.kilometrageCompteur);
        $('#adresse').val(oConso.adresse);
        $('#idConso').val(oConso.idConso);

        $('#modalAjoutConso').modal('show')
            .find('#myModalLabel').html('Éditer une consommation');
    }

    function getTitle(d) {
        var date = '',
            data = {},
            pad = '00',
            month,
            day,
            type = 0;

        $.each(d, function(i, val) {
            if(i === "datePlein") {
                day = val.getDate();
                month = (val.getMonth() + 1);
                date = (pad.substring(0, pad.length - day.toString().length) + day.toString()) + '/' + (pad.substring(0, pad.length - month.toString().length) + month.toString()) + '/' + val.getFullYear();
            } else {
                data = val;
                type = i;
            }
        });

        return date + '<br />' + data + ' ' + uniteTooltip[type];
    }

    function getToolTip(d) {
        var e = d3.event,
            offset = $('#layout-work').offset();

        $('#proxyToolTip').css({
            top: e.pageY + 20,
            left: e.pageX - offset.left + 20
        })
            .attr('title', getTitle(d))
            .attr('data-html', 'true')
            .attr('data-original-title', getTitle(d))
            .tooltip('show');
    }

    function initD3(nomGraph) {
        if(typeof d3 === 'undefined') {
            alert('La librairie D3 n\'est pas initialisé');
            return false;
        }

        var divSvg = d3.select("#divSvg" + Gosyl.Common.STR_ucwords(nomGraph));
        var margin = {
            top: 15,
            right: 15,
            bottom: 15,
            left: 15
        };
        var dim = {
            width: $(document).width() - 3 * (margin.left + margin.right) - 4,
            height: $(document).height() - Math.round($('body').height())
        };

        var svg = divSvg.append('svg');
        svg.attr('width', dim.width);
        svg.attr('height', dim.height);

        var g = svg.append("g").attr("transform", "translate(" + margin.left + "," + margin.top + ")");

        var width = +svg.attr('width') - margin.left - margin.right,
            height = +svg.attr('height') - margin.top - margin.bottom;

        var parseTime = d3.timeParse("%d/%m/%Y");

        var dataSet = dataEvo[nomGraph];

        dataSet.forEach(function(d) {
            d.datePlein = parseTime(d.datePlein);
            d[nomGraph] = +d[nomGraph];
        });

        var x = d3.scaleTime()
            .rangeRound([0, width]);

        var y = d3.scaleLinear()
            .rangeRound([height, 0]);

        var line = d3.line()
            .x(function(d) { return x(d.datePlein); })
            .y(function(d) { return y(d[nomGraph]); })
            .curve(d3.curveMonotoneX);

        x.domain(d3.extent(dataSet, function(d) { return d.datePlein; }));
        y.domain(d3.extent(dataSet, function(d) { return d[nomGraph]; }));

        g.append("g")
            .attr("transform", "translate(0," + height + ")")
            .call(d3.axisBottom(x))
        // .select(".domain")
        // .remove()
        ;

        var  axeY = g.append("g")
            .call(d3.axisLeft(y));

        axeY.selectAll(".tick")
            .selectAll("text")
            .attr('x', '30')
            .attr('y', -6);

        axeY.append("text")
            .attr("fill", "#000")
            .attr("transform", "rotate(-90)")
            .attr("y", 35)
            .attr("dy", "0.71em")
            .attr("text-anchor", "end")
            .text(txtLegend[nomGraph]);

        g.append("path")
            .datum(dataSet)
            .attr("fill", "none")
            .attr("stroke", "steelblue")
            .attr("stroke-linejoin", "round")
            .attr("stroke-linecap", "round")
            .attr("stroke-width", 1.5)
            .attr("d", line);

        g.selectAll('.dot')
            .data(dataSet)
            .enter()
            .append('circle')
            .attr('class', 'dot')
            .attr("cx", function(d) {
                return x(d.datePlein);
            })
            .attr("cy", function(d) {
                return y(d[nomGraph]);
            })
            .attr('r', 5)
            .attr('style', 'cursor: pointer;')
            .on('mousemove', getToolTip)
            .on('mouseleave', unsetToolTip);
    }

    function initMap() {
        $(function() {
            var i;
            while(typeof google === 'undefined') {
                i++;
            }

            var mapOptions = {
                zoom: 6,
                center: new google.maps.LatLng(46.227638, 2.213749),
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };

            Gosyl.MyCarbuConso.Default.Stat.map = new google.maps.Map(document.getElementById('divMaps'), mapOptions);
            map = Gosyl.MyCarbuConso.Default.Stat.map;

            ajoutePointConso();
        });

    }

    function ajoutePointConso() {
        var locations = [],
            iconBase = 'https://maps.google.com/mapfiles/kml/shapes/';
        $.each(dataConso, function(i, conso) {
            var content;
            locations.push({
                lat: parseFloat(conso.gpsLat),
                lng: parseFloat(conso.gpsLong)
            });

            content = '<div id="content'+conso.gpsLat + '_' + conso.gpsLong+'">'+
                    '<h5>'+conso.adresse.replace(/\n/g, '<br />')+'</h5>'+
                    '<div id="bodyContent">'+
                        '<table class="table table-bordered" id="tab'+conso.gpsLat + '_' + conso.gpsLong+'">'+
                            '<thead>'+
                                '<tr>'+
                                    '<td>Date du plein</td>'+
                                    '<td>Volume (l)</td>'+
                                    '<td>Prix (€)</td>'+
                                    '<td>Prix au litre (€/l)</td>'+
                                '</tr>'+
                            '</thead>'+
                            '<tbody>'+

                            '</tbody>'+
                        '</table>'+
                    '</div>'+
                '</div>';

            var infoWindow = new google.maps.InfoWindow({
                content: content
            });

            var marker = new google.maps.Marker({
                position: {lat: parseFloat(conso.gpsLat), lng: parseFloat(conso.gpsLong)},
                map: map,
                icon: iconBase + 'gas_stations_maps.png'
            });

            marker.addListener('mouseover', function() {
                showInfoStation(infoWindow, conso, marker)
            });
            marker.addListener('click', function() {
                showInfoStation(infoWindow, conso, marker)
            });
            marker.addListener('mouseout', function() {
                infoWindow.close(map, marker);
            });
        });
    }

    function showInfoStation(infoWindow, conso, marker) {
        infoWindow.open(map, marker);
        infoWindow.addListener('domready', function() {
            var div = $('[id^="content"]').get(0);
            var contenu = '';

            $.each(points[conso.gpsLat + '_' + conso.gpsLong], function(i, val) {
                if(i !== 'adresse') {
                    contenu += '<tr>';
                    contenu += '<td>'+i+'</td><td>'+val.quantite+'</td><td>'+val.prix+'</td><td>'+val.prixAuLitre+'</td>';
                    contenu += '</tr>'
                }
            });

            $(div).find('tbody').html(contenu);
        });
    }

    function setBtnActionForConso(oConso) {//console.log(oConso);
        var contenu = '';

        contenu += '<div class="container-fluid">'+
            '<div class="row">'+
                '<div class="col-xs-6" style="text-align: center">'+
                    '<i class="fa fa-pencil" style="font-size: 1.5em; cursor: pointer;" title="Éditer cette consommation" id="btnEditConso_' + oConso.idConso + '" data-id="'+oConso.idConso+'"></i>'+
                '</div>'+
                '<div class="col-xs-6" style="text-align: center">'+
                    '<i id="btnSuppressionConso_' + oConso.idConso + '" data-id="' + oConso.idConso + '" class="fa fa-trash" style="font-size: 1.5em;  cursor: pointer;" title="Supprimer cette consommation"></i>'+
                '</div>'+
            '</div>'+
        '</div>';
        return contenu;
    }

    function suppressionConso(e) {
        e.stopPropagation();

        var id = $(this).data('id');

        $('#modalDmdeSupprConso').modal('show');

        $('#btnValidSupprConso').on('click', function(e) {
            e.stopPropagation();
            $.ajax({
                url: Gosyl.Common.rootPath + 'carbu/ajax/deleteconso/' + id,
                data: {
                    'id': id
                },
                dataType: 'json',
                cache: false,
                type: 'post'
            }).then(function(data) {
                if(!data.error) {
                    window.location.reload();
                }
            });
        });
    }

    function setDataEvo(data) {
        dataConso = data;

        var compteur = 0,
            consoTotale = 0,
            consoMoyenneTotale = 0,
            //consoMoyenne = 0,
            distanceTotale = 0,
            prixAuLitreTotal = 0,
            prixTotal = 0//,
            //prixMoyen = 0
            ;

        $.each(data, function(i, enreg) {
            var prix = enreg.prix ? enreg.prix : 0,
                quantite = enreg.quantite ? enreg.quantite : 0,
                distance = enreg.distance ? enreg.distance : 0,
                prixAuLitre = Math.round(prix / quantite * 1000) / 1000,
                conso = Math.round(((quantite / distance) * 100) * 100) / 100;

            prixTotal += parseFloat(prix);
            prixAuLitreTotal += prixAuLitre;
            consoMoyenneTotale += conso;
            consoTotale += parseFloat(quantite);
            distanceTotale += parseFloat(distance);

            dataEvo.prix.push({
                datePlein: enreg.datePlein,
                prix: prixAuLitre
            });

            dataEvo.conso.push({
                datePlein: enreg.datePlein,
                conso: conso
            });

            if(typeof points[enreg.gpsLat + '_' + enreg.gpsLong] !== 'undefined') {
                points[enreg.gpsLat + '_' + enreg.gpsLong][enreg.datePlein] = {
                    quantite: enreg.quantite,
                    prix: enreg.prix,
                    prixAuLitre: prixAuLitre
                }
            } else {
                points[enreg.gpsLat + '_' + enreg.gpsLong] = {
                    adresse: enreg.adresse
                };

                points[enreg.gpsLat + '_' + enreg.gpsLong][enreg.datePlein] = {
                    quantite: enreg.quantite,
                    prix: enreg.prix,
                    prixAuLitre: prixAuLitre
                }
            }

            compteur++;
        });

        consoMoyenne = consoMoyenneTotale ? Math.round(consoMoyenneTotale / compteur * 100 ) / 100 : 0;
        prixMoyen = prixAuLitreTotal ? Math.round(prixAuLitreTotal / compteur * 1000 ) / 1000 : 0;

        $('#statistiques').html(
            'Consommation moyenne : ' + consoMoyenne.toString() + ' l/100km<br />'+
            'Prix au litre moyen : ' + prixMoyen.toString() + ' €/l<br />'+
            'Total dépensé : ' + prixTotal + ' €<br/>'+
            'Consommation totale : ' + (Math.round(consoTotale * 100) / 100) + ' l<br />'+
            'Distance totale : ' + (Math.round(distanceTotale * 10) / 10).toString() + ' km'
        );

        initD3('prix');
        initD3('conso');
    }

    function unsetToolTip() {
        $('#proxyToolTip')
            .attr('title', '')
            .attr('data-original-title', '')
            .tooltip('hide');
    }

    return {
        init: init,
        initMap: initMap,
        setBtnActionForConso: setBtnActionForConso,
        setDataEvo: setDataEvo,
        idModeleUser: idModeleUser,
        map: map
    };
})(jQuery);

$(function() {
    Gosyl.MyCarbuConso.Default.Stat.init();
});