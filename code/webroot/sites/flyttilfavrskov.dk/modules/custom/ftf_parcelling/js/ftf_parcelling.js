(function (drupalSettings) {
  if(drupalSettings.ftf_parcelling.parcelling_type && drupalSettings.ftf_parcelling.parcelling_id) {
    new ParcelMap('parcel-map-body', drupalSettings.ftf_parcelling.parcelling_type, drupalSettings.ftf_parcelling.parcelling_id);
  }
  if(drupalSettings.ftf_parcelling.gis_minimap) {
    for(let key in drupalSettings.ftf_parcelling.gis_minimap) {
      MiniMap.createMiniMap({mapDiv: 'gis_minimap_'+key, minimapId: drupalSettings.ftf_parcelling.gis_minimap[key]});

    }
  }
})(drupalSettings);
