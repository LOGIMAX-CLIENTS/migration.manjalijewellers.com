# Recipe: Tagging Stock Image Medium-Large View and Zoom
## Metadata
- **Pattern ID**: PAT-UI-002
- **Severity**: LOW
- **Modules Affected**: Tagging
- **Auto-fixable**: Yes

## Client Scope
- **Applies to**: ALL
- **Reason**: All tagging view implementations share the same assets/js/ret_tagging.js file structure.

## Created By
- **Developer**: Antigravity
- **Client**: AMS-RetailAdmin
- **Date**: 2026-06-03
- **Source Bug ID**: N/A

## Symptom
The user wanted to adjust the image preview layout in the Tagging form to display stock code images in an increased size (`200px × 200px`) while still allowing a full screen larger view on demand.

## Root Cause
1. Stock image thumbnails in the modal were hardcoded to a tiny `100px × 100px` width/height in `assets/js/ret_tagging.js` inside a standard bootstrap grid (`col-md-3`), leaving massive empty space.
2. Clicking the preview button appended elements directly to `#stock_images` without clearing the container first, causing duplicate images to stack.

## Detection
```command
grep -rn "width: 100px;height: 100px;" assets/js/ret_tagging.js
```

## Files
- `assets/js/ret_tagging.js`
- `application/views/tagging/form.php`

## Fix

### Before
In `assets/js/ret_tagging.js`:
```javascript
$('#view_stock_img').on('click', function () {

	var stock_images = $("#stock_image").val().split("#");
	// AMS-440: Also get secondary images
	var secondary_images = $("#secondary_image").val() ? $("#secondary_image").val().split("#") : [];

	if (stock_images.length > 0 || secondary_images.length > 0) {

		$('#stockimageModal').modal('show');

		// Primary (Photo) images
		if (stock_images.length > 0 && stock_images[0] != '') {
			$('#stock_images').append('<div class="col-md-12" style="margin-bottom:4px;"><b style="color:#333;font-size:13px;">Primary (Photo)</b></div>');
		}
		$.each(stock_images, function (key, items) {

			if (items != '') {

				var img_src = _img_base + 'assets/img/stock_code/' + items;

				var div = document.createElement("div");

				div.setAttribute('class', 'col-md-3');

				div.innerHTML += "<img class='thumbnail' src='" + img_src + "'" +

					"style='width: 100px;height: 100px;'/>";

				$('#stock_images').append(div);

			}

		});

		// AMS-440: Secondary (SKU) images
		if (secondary_images.length > 0 && secondary_images[0] != '') {
			$('#stock_images').append('<div class="col-md-12" style="margin-top:10px;margin-bottom:4px;border-top:1px solid #eee;padding-top:8px;"><b style="color:#333;font-size:13px;">Secondary (SKU)</b></div>');
			$.each(secondary_images, function (key, items) {
				if (items != '') {
					var img_src = _img_base + 'assets/img/stock_code/' + items;
					var div = document.createElement("div");
					div.setAttribute('class', 'col-md-3');
					div.innerHTML += "<img class='thumbnail' src='" + img_src + "'" +
						"style='width: 100px;height: 100px;'/>";
					$('#stock_images').append(div);
				}
			});
		}

	}

});
```

### After
In `assets/js/ret_tagging.js`:
```javascript
$('#view_stock_img').on('click', function () {

	var stock_images = $("#stock_image").val().split("#");
	// AMS-440: Also get secondary images
	var secondary_images = $("#secondary_image").val() ? $("#secondary_image").val().split("#") : [];

	if (stock_images.length > 0 || secondary_images.length > 0) {

		$('#stockimageModal').modal('show');
		$('#stock_images').empty(); // Clear existing preview to avoid duplicates

		// Primary (Photo) images
		if (stock_images.length > 0 && stock_images[0] != '') {
			$('#stock_images').append('<div class="col-md-12" style="margin-bottom:8px;"><b style="color:#333;font-size:14px;">Primary (Photo)</b></div>');
		}
		$.each(stock_images, function (key, items) {

			if (items != '') {

				var img_src = _img_base + 'assets/img/stock_code/' + items;

				var div = document.createElement("div");

				div.setAttribute('class', 'col-md-3'); // 4 items per row

				div.innerHTML += "<img class='thumbnail img-hover-zoom img-responsive' src='" + img_src + "'" +

					"style='width: 200px; height: 200px; object-fit: cover; border-radius: 6px; cursor: pointer;'/>";

				$('#stock_images').append(div);

			}

		});

		// AMS-440: Secondary (SKU) images
		if (secondary_images.length > 0 && secondary_images[0] != '') {
			$('#stock_images').append('<div class="col-md-12" style="margin-top:15px;margin-bottom:8px;border-top:1px solid #eee;padding-top:12px;"><b style="color:#333;font-size:14px;">Secondary (SKU)</b></div>');
			$.each(secondary_images, function (key, items) {
				if (items != '') {
					var img_src = _img_base + 'assets/img/stock_code/' + items;
					var div = document.createElement("div");
					div.setAttribute('class', 'col-md-3'); // 4 items per row
					div.innerHTML += "<img class='thumbnail img-hover-zoom img-responsive' src='" + img_src + "'" +
						"style='width: 200px; height: 200px; object-fit: cover; border-radius: 6px; cursor: pointer;'/>";
					$('#stock_images').append(div);
				}
			});
		}

	}

});
```

And in `application/views/tagging/form.php`:
```css
    	.img-hover-zoom {
    		transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    	}

    	.img-hover-zoom:hover {
    		transform: scale(1.04) translateY(-2px);
    		box-shadow: 0 10px 20px rgba(0,0,0,0.15);
    		cursor: zoom-in;
    	}
```

And we added a global dynamic lightbox viewer `showImageLightboxTagging` inside `assets/js/ret_tagging.js`.

## Verification
1. Go to `http://localhost/AMS-RetailAdmin/index.php/admin_ret_tagging/tagging/add#`
2. Select a stock code that contains images.
3. Click the View Eye Icon button (`#view_stock_img`).
4. The stock images are now loaded inside a `col-md-3` grid at an increased `width: 200px; height: 200px;` size with hover scaling effects.
5. Click on any stock image thumbnail: a full-screen dynamic lightbox overlay appears displaying the image in its natural proportions.
6. Click outside or click `×` or press `Esc` to close the lightbox.
7. Click the View Eye Icon button multiple times; check that images do not duplicate.

## Notes
The lightbox overlay mimics the e-commerce image gallery style from the shopify integrations module to keep visual identity consistency across the AMS suite.
