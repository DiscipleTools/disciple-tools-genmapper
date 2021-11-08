/**
 * Update the counts when the group page member list is refreshed.
 * This is only loaded on the group page.
 */
(function () {
  let believerCountInput = document.querySelector('#believer_count')
  let baptizedCountInput = document.querySelector('#baptized_count')

  function populateMetrics(event) {
    if (!event) {
      return
    }
    believerCountInput.value = event.detail.believer_count
    baptizedCountInput.value =  event.detail.baptized_count
  }

  document.addEventListener("dt-member-list-populated", populateMetrics);

  populateMetrics()
})();

