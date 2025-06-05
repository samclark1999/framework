document.addEventListener('DOMContentLoaded', (e) => {
            acf.addAction('new_field/key=field_6596f612762a3', function () {
                const postTypes = acf.getField('field_6596f612762a3');
                const taxonomyFilter = acf.getField('field_6596f63a762a4');
                const taxonomyListing = acf.getField('field_taxonomies_pre_filter_key');
                const termsListing = acf.getField('field_terms_pre_filter_key');

                // console.log('Post Types:', postTypes);
                // console.log('Taxonomy Filter:', taxonomyFilter);
                // console.log('Taxonomy Listing:', taxonomyListing);
                // console.log('Terms Listing:', termsListing);

                if (postTypes) {
                    if (taxonomyFilter) {
                        filterTaxonomyByPostType(postTypes, taxonomyFilter);
                        postTypes.on('change', function (e) {
                            // console.log('Post Types changed:', postTypes.val());
                            filterTaxonomyByPostTypeUpdate(postTypes, taxonomyFilter);
                        });
                    }

                    if (taxonomyListing) {
                        filterTaxonomyByPostType(postTypes, taxonomyListing);
                        postTypes.on('change', function (e) {
                            // console.log('Post Types changed for Taxonomy Listing:', postTypes.val());
                            filterTaxonomyByPostTypeUpdate(postTypes, taxonomyListing);
                        });
                    }

                    if (termsListing) {
                        // filterTermsByTaxonomies(taxonomyListing, termsListing);
                        taxonomyListing.on('change', function (e) {
                            // console.log('Post Types changed for Terms Listing:', taxonomyListing.val());
                            filterTermsByTaxonomiesUpdate(taxonomyListing, termsListing);
                        });
                    }
                }
            });

            const filterTaxonomyByPostType = (postTypes, taxonomyFilter) => {
                // console.log('Filtering Taxonomy by Post Type:', postTypes.val());
                let data = new FormData();
                data.append('action', 'lvl_get_taxonomy');

                fetch(app_localized?.ajax_url, {
                    method: 'POST',
                    credentials: 'same-origin',
                    body: data,
                })
                    .then(response => {
                        if (!response.ok) {
                            console.error('Error getting taxonomy terms');
                            return;
                        }
                        return response.json();
                    })
                    .then(response => {
                        // console.log('Taxonomy Response:', response);
                        if (response) {
                            if (response?.taxonomies) {
                                taxonomyFilter.$el[0].dataset.taxonomies = JSON.stringify(response.taxonomies);
                            }
                        }
                    })
                    .then(() => {
                        filterTaxonomyByPostTypeUpdate(postTypes, taxonomyFilter);
                    })
                    .catch(error => {
                        console.error('Error getting taxonomy terms', error);
                    });
            };

            const filterTaxonomyByPostTypeUpdate = (postTypes, taxonomyFilter) => {
                // console.log('Updating Taxonomy Filter:', postTypes.val());
                let checkedPostTypes = postTypes.val() ? postTypes.val() : [];
                let taxonomies = taxonomyFilter.$el[0].dataset?.taxonomies;

                if (taxonomies) {
                    taxonomies = JSON.parse(taxonomies);
                    // console.log('Parsed Taxonomies:', taxonomies);

                    let el = taxonomyFilter.$el;
                    let inputs = el.find('input');

                    if (postTypes.val()) {
                        el.find('li').hide();
                    } else {
                        el.find('li').show();
                        return;
                    }

                    inputs.each((index, input) => {
                        let inputEl = jQuery(input);
                        let value = inputEl.val();

                        if (taxonomies?.[value]) {
                            if (taxonomies[value].some(r => checkedPostTypes.includes(r))) {
                                inputEl.closest('li').show();
                            }
                        }
                    });

                    inputs.each((index, input) => {
                        let inputEl = jQuery(input);
                        if (!inputEl.closest('li').is(':visible')) {
                            inputEl.prop('checked', false);
                        }
                    });
                }
            };

            const filterTermsByTaxonomies = (taxonomyFilter, termsListing) => {
                // console.log('Filtering Terms by Taxonomies:', taxonomyFilter.val());
                let selectedTaxonomies = taxonomyFilter.val() ? taxonomyFilter.val() : [];
                if (!selectedTaxonomies.length) {
                    termsListing.$el.find('li').show();
                    return;
                }

                let data = new FormData();
                data.append('action', 'lvl_get_taxonomy_terms');
                data.append('taxonomy', JSON.stringify(selectedTaxonomies));

                fetch(app_localized?.ajax_url, {
                    method: 'POST',
                    credentials: 'same-origin',
                    body: data,
                })
                    .then(response => {
                        if (!response.ok) {
                            console.error('Error getting taxonomy terms');
                            return;
                        }
                        return response.json();
                    })
                    .then(response => {
                        // console.log('Terms Response:', response);
                        if (response) {
                            termsListing.$el[0].dataset.terms = JSON.stringify(response);
                        }
                    })
                    .then(() => {
                        filterTermsByTaxonomiesUpdate(taxonomyFilter, termsListing);
                    })
                    .catch(error => {
                        console.error('Error getting taxonomy terms', error);
                    });
            };

            const filterTermsByTaxonomiesUpdate = (taxonomyFilter, termsListing) => {
                // console.log('Updating Terms Listing:', taxonomyFilter.val());
                let selectedTaxonomies = taxonomyFilter.val() ? taxonomyFilter.val() : [];

                if (!selectedTaxonomies.length) {
                    termsListing.$el.find('li').show();
                    termsListing.$el.find('select').empty(); // Clear the multi-select field
                    return;
                }

                let data = new FormData();
                data.append('action', 'lvl_get_taxonomy_terms');
                data.append('taxonomy', JSON.stringify(selectedTaxonomies));

                // console.log("Selected Taxonomies:", selectedTaxonomies);

                fetch(app_localized?.ajax_url, {
                    method: 'POST',
                    credentials: 'same-origin',
                    body: data,
                })
                    .then(response => {
                        if (!response.ok) {
                            console.error('Error fetching taxonomy terms');
                            return;
                        }
                        return response.json();
                    })
                    .then(terms => {
                        // console.log('Fetched Terms:', terms);
                        if (terms) {
                            termsListing.$el[0].dataset.terms = JSON.stringify(terms);

                            let el = termsListing.$el;
                            let inputs = el.find('input');
                            let select = el.find('select'); // Target the multi-select field

                            el.find('li').hide();
                            select.empty(); // Clear existing options

                            // Populate the multi-select field with fetched terms
                            Object.keys(terms).forEach(termId => {
                                let termName = terms[termId];
                                select.append(new Option(termName, termId));
                            });

                            inputs.each((index, input) => {
                                let inputEl = jQuery(input);
                                let value = inputEl.val();

                                if (terms?.[value]) {
                                    if (selectedTaxonomies.some(taxonomy => terms[value].includes(taxonomy))) {
                                        inputEl.closest('li').show();
                                    }
                                }
                            });

                            inputs.each((index, input) => {
                                let inputEl = jQuery(input);
                                if (!inputEl.closest('li').is(':visible')) {
                                    inputEl.prop('checked', false);
                                }
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching taxonomy terms:', error);
                    });
            };
        });