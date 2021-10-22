(function () {
  let believerCountInput = document.querySelector('#believer_count')
  let baptizedCountInput = document.querySelector('#baptized_count')

  function populateMetrics(event) {
    believerCountInput.value = event.detail.believer_count
    baptizedCountInput.value =  event.detail.baptized_count
  }

  document.addEventListener("dt-member-list-populated", populateMetrics);

  populateMetrics()
})();

