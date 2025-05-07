import AjaxRequest from "@typo3/core/ajax/ajax-request.js";
import Icons from '@typo3/backend/icons.js';
import Notification from "@typo3/backend/notification.js";

document.querySelectorAll('.favorite-js').forEach(function (button) {
  button.addEventListener('click', function (ev) {
    new AjaxRequest(TYPO3.settings.ajaxUrls.favoriteelement_toggle)
      .withQueryArguments({id: button.dataset.favorite})
      .get()
      .then(async function (response) {
        const resolved = await response.resolve();

        Notification.success('Success', resolved.result.title, 5);
        Icons.getIcon(resolved.result.icon, Icons.sizes.small).then(function (spinner) {
          button.innerHTML = spinner;
        });
      });
  })
})
