(function ($) {
  /**
   * Update the counts when the group page member list is refreshed.
   * This is only loaded on the group page.
   */
  let believerCountInput = document.querySelector('#believer_count')
  let baptizedCountInput = document.querySelector('#baptized_count')
  let showFieldsTrigger = document.querySelector('#show-metrics-fields')
  let extraFields = document.querySelector('#metrics-extra-fields')

  if (believerCountInput && baptizedCountInput) {
    function populateMetrics(event) {
      if (!event) {
        return
      }
      believerCountInput.value = event.detail.believer_count
      baptizedCountInput.value =  event.detail.baptized_count
    }

    populateMetrics()
    document.addEventListener("dt-member-list-populated", populateMetrics);
  }

  if (showFieldsTrigger && extraFields) {
      showFieldsTrigger.addEventListener('click', () => {
        extraFields.style.display =  (extraFields.style.display === "none") ? "block" : "none";
        showFieldsTrigger.style.display =  (showFieldsTrigger.style.display === "block") ? "none" : "block";
        window.dispatchEvent(new Event('resize'));
        console.log($(extraFields).closest('.grid'))
        $(extraFields).closest('.grid').masonry()
      })
  }
})(jQuery);

