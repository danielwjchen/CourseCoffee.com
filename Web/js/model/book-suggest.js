/**
 * @file
 * Get list of suggested readings for a class
 */
window.BookSuggest = function(regionName) {
	region = $(regionName);

	/**
	 * Generate output for each offer type
	 *
	 * @param string type
	 * @param object offer
	 *
	 * @return string html
	 */
	generateOfferOutput = function(type, offer) {

		offerHtml = '<ul>';
		offerHtml += '<li class="offer-type">' + type + '</li>';
		vendorHtml = '';
		$.each(offer, function(index, value) {
			if (value['price']) {
				vendorHtml += '<li><a href="' + value['link'] + '" target="_blank" >' +
					'<span class="vendor">' + index + '</span>' +
					'<span class="price">&#36;' + value['price'] + '</span>' +
				'</a></li>';
			}
			// debug
			// console.log(index);
			// console.log(value);
		});
		if (vendorHtml == '') {
			return '';
		}

		offerHtml += vendorHtml + '</ul>';

		return offerHtml;
	}

	/**
	 * Get list of books from different vendors
	 *
	 * @param sectionId
	 */
	this.getBookList = function(sectionId) {
		region.addClass('loading');
		$.ajax({
			url: '/college-class-reading',
			type: 'post',
			data: 'section_id=' + sectionId,
			success: function(response) {
				region.removeClass('loading');
				if (response.list) {
					html = '<ul>';
					$.each(response.list, function(title, value) {
						newOffers     = generateOfferOutput('buy new', value['offers']['new']);
						usedOffers    = generateOfferOutput('buy used', value['offers']['used']);
						retanlOoffers = generateOfferOutput('rent', value['offers']['rental']);
						html += '<li class="book">' +
							'<img src="' + value['image'] + '" class="cover" />' +
							'<div class="info">' +
								'<span class="title">' + title  + '</span>' +
								'<div class="offer">' + newOffers + usedOffers + retanlOoffers + '</div>' +
							'</div>' +
						'</li>';
						// console.log(title);
						// console.log(value);
					});
					html += '</ul>';

					region.html(html);
				}
			}
		});
	}
}
