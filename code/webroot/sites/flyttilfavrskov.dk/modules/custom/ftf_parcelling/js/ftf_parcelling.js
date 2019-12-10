
(function () {
  console.log([drupalSettings.ftf_parcelling.parcelling_type, drupalSettings.ftf_parcelling.parcelling_id]);
  new ParcelMap('parcel-map-body', drupalSettings.ftf_parcelling.parcelling_type, drupalSettings.ftf_parcelling.parcelling_id);
})();
