(function () {
  let believerCountInput = document.querySelector('#believer_count')
  let baptizedCountInput = document.querySelector('#baptized_count')

  function populateMetrics() {
    let post = window.detailsSettings.post_fields
    believerCountInput.value = post.believer_count
    baptizedCountInput.value = post.baptized_count
  }

  document.addEventListener("dt-member-list-populated", populateMetrics);

  populateMetrics()
})();

