/**
 * @file
 * Get list of suggested readings for a class
 */
window.BookSuggest = function(regionName) {
	var region = $(regionName);
	var cache  = new Cache();

	/**
	 * Generate output for each offer type
	 *
	 * @param string type
	 * @param object offer
	 *
	 * @return string html
	 */
	var generateOfferOutput = function(type, offer) {

		var offerHtml = '<ul><li class="offer-type">' + type + '</li>';
		var vendorHtml = '';
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
		region.empty();
		region.addClass('loading');
		region.html('<h3>Please wait while we find the lowest price on your textbooks.</h3>' +
			'<span class="double-underline"><a href="#">how?</a></span>');
		$('.double-underline a', region).click(function(e) {
			e.preventDefault();
			var content = '<h2>How does this work?</h2>' +
				"<p>It's a simple idea, really. We capture the most lengendary textbook vendors on the internet (Amazon, Chegg, ValoreBooks, eCampus, e.t.c.), and cage them in a gladiator battle royal duel to provide you the best deal possible, pokemon style.<p>" +
				'<div class="explanation">' +
					'<img src="/images/gundamu.png" />' +
					"<p>*Because of copyright and trademark reasons, we can't legally show logos of said compaies or pictures of our favorite pokemon. We do have this cool robot, though.</p>" +
				'</div>' +
				"<h3>No worries. No feelings is hurt during the production of this awesome website.</h3>";
			;
			dialog.open('book-search-explained', content);
			$('.dialog-close').click(function(e) {
			e.preventDefault();
				dialog.close();
			});
		});
		var cachedValue = cache.get(sectionId);
		if (cachedValue) {
			region.removeClass('loading');
			region.html(cachedValue);
			return ;
		}

		$.ajax({
			url: '/college-class-reading',
			type: 'post',
			cache: true,
			data: 'section_id=' + sectionId,
			success: function(response) {
				region.removeClass('loading');
				var html = '<h3>' + response.message + '</h3>';
				if (response.list) {
					html += '<ul>';
					$.each(response.list, function(title, value) {
						var newOffers     = generateOfferOutput('buy new', value['offers']['new']);
						var usedOffers    = generateOfferOutput('buy used', value['offers']['used']);
						var retanlOoffers = generateOfferOutput('rent', value['offers']['rental']);
						var bookCover = value['image'] != '' ? '<img src="' + value['image'] + '" class="cover" />' : '';
						html += '<li class="book">' +
							bookCover + 
							'<div class="info">' +
								'<span class="title">' + title  + '</span>' +
								'<div class="offer">' + newOffers + usedOffers + retanlOoffers + '</div>' +
							'</div>' +
						'</li>';
						// console.log(title);
						// console.log(value);
					});
					html += '</ul>';


					// debug
					// console.log('book suggest cache');
					// console.log(cache);

				}
				cache.set(sectionId, html);
				region.html(html);
			}
		});
	}

	/**
	 * Get all required books for a list of sections
	 */
	this.getAllBookList = function(sectionForm) {
		region.addClass('loading');
		var sectionId = '';
		var html = '';
		$(sectionForm).each(function(i, el) {
			sectionId = $(el).val();
			$.ajax({
				url: '/college-class-reading',
				type: 'post',
				cache: true,
				data: 'section_id=' + sectionId,
				success: function(response) {
					if (response.list) {
						html += '<ul>';
						$.each(response.list, function(title, value) {
							var newOffers     = generateOfferOutput('buy new', value['offers']['new']);
							var usedOffers    = generateOfferOutput('buy used', value['offers']['used']);
							var retanlOoffers = generateOfferOutput('rent', value['offers']['rental']);
							var bookCover = value['image'] != '' ? '<img src="' + value['image'] + '" class="cover" />' : '';
							html += '<li class="book">' +
								bookCover + 
								'<div class="info">' +
									'<span class="title">' + title  + '</span>' +
									'<div class="offer">' + newOffers + usedOffers + retanlOoffers + '</div>' +
								'</div>' +
							'</li>';
							// console.log(title);
							// console.log(value);
						});
						html += '</ul>';
						region.append(html);
						html = '';


						// debug
						// console.log('book suggest cache');
						// console.log(cache);

					}
					cache.set(sectionId, html);
				}
			});
		});
		region.removeClass('loading');

	}
}
