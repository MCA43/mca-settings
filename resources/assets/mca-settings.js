(function () {
    'use strict';

    function initShellNav() {
        var btn = document.getElementById('mcaUiMenuBtn');
        var nav = document.getElementById('mcaUiNav');
        if (!btn || !nav) return;

        btn.addEventListener('click', function () {
            var open = nav.classList.toggle('is-open');
            btn.setAttribute('aria-expanded', open ? 'true' : 'false');
            btn.innerHTML = open
                ? '<svg class="mca-ui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" d="M6 6l12 12M18 6L6 18"/></svg>'
                : '<svg class="mca-ui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/></svg>';
        });

        nav.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                if (window.matchMedia('(max-width: 899px)').matches) {
                    nav.classList.remove('is-open');
                    btn.setAttribute('aria-expanded', 'false');
                }
            });
        });
    }

    function bindIconPreview(input) {
        if (!input || input.dataset.mcaSettIconBound) {
            return;
        }

        input.dataset.mcaSettIconBound = '1';
        var preview = input.closest('[data-mca-sett-repeater-row]')
            ?.querySelector('[data-mca-sett-icon-preview]');

        if (!preview) {
            return;
        }

        var render = function () {
            var value = (input.value || '').trim();
            preview.innerHTML = value !== '' ? '<i class="' + value.replace(/"/g, '') + '"></i>' : '';
        };

        input.addEventListener('input', render);
        render();
    }

    function initRepeaters() {
        document.querySelectorAll('[data-mca-sett-repeater]').forEach(function (root) {
            var body = root.querySelector('[data-mca-sett-repeater-body]');
            var templateId = root.getAttribute('data-mca-sett-repeater-template');
            var template = templateId ? document.getElementById(templateId) : null;
            var addButton = root.querySelector('[data-mca-sett-repeater-add]');

            if (!body || !template || !addButton) {
                return;
            }

            var nextIndex = body.querySelectorAll('[data-mca-sett-repeater-row]').length;

            function reindexRows() {
                body.querySelectorAll('[data-mca-sett-repeater-row]').forEach(function (row, index) {
                    row.querySelectorAll('[name]').forEach(function (input) {
                        input.name = input.name.replace(/\[\d+\]/, '[' + index + ']');
                    });
                });
                nextIndex = body.querySelectorAll('[data-mca-sett-repeater-row]').length;
            }

            function bindRow(row) {
                row.querySelectorAll('[data-mca-sett-icon-input]').forEach(bindIconPreview);

                var removeButton = row.querySelector('[data-mca-sett-repeater-remove]');
                if (!removeButton) {
                    return;
                }

                removeButton.addEventListener('click', function () {
                    var rows = body.querySelectorAll('[data-mca-sett-repeater-row]');
                    if (rows.length <= 1) {
                        row.querySelectorAll('input, select').forEach(function (input) {
                            input.value = input.tagName === 'SELECT' ? input.options[0]?.value || '' : '';
                        });
                        row.querySelectorAll('[data-mca-sett-icon-input]').forEach(bindIconPreview);
                        return;
                    }

                    row.remove();
                    reindexRows();
                });
            }

            body.querySelectorAll('[data-mca-sett-repeater-row]').forEach(bindRow);

            addButton.addEventListener('click', function (event) {
                event.preventDefault();

                var clone = template.content.cloneNode(true);
                var row = clone.querySelector('[data-mca-sett-repeater-row]') || clone.firstElementChild;
                if (!row) {
                    return;
                }

                row.querySelectorAll('[name]').forEach(function (input) {
                    input.name = input.name.replace(/__INDEX__/g, String(nextIndex));
                });

                body.appendChild(row);
                bindRow(row);
                nextIndex += 1;
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initShellNav();
        initRepeaters();
        initLocationSelects();
    });

    function initLocationSelects() {
        document.querySelectorAll('[data-mca-sett-location][data-mca-sett-location-mode="select"]').forEach(function (root) {
            var apiRaw = root.getAttribute('data-mca-sett-location-api');
            var api = apiRaw ? JSON.parse(apiRaw) : {};
            var citySelect = root.querySelector('[data-mca-sett-location-city]');
            var districtSelect = root.querySelector('[data-mca-sett-location-district]');
            var neighborhoodSelect = root.querySelector('[data-mca-sett-location-neighborhood]');
            var cityNameInput = root.querySelector('[data-mca-sett-location-city-name]');
            var districtNameInput = root.querySelector('[data-mca-sett-location-district-name]');
            var neighborhoodNameInput = root.querySelector('[data-mca-sett-location-neighborhood-name]');
            var initialEl = root.querySelector('[data-mca-sett-location-initial]');
            var initial = initialEl ? JSON.parse(initialEl.textContent || '{}') : {};

            if (!citySelect || !api.cities) {
                return;
            }

            function normalizeOptions(payload) {
                var rows = Array.isArray(payload) ? payload : (payload && payload.data ? payload.data : []);

                return rows.map(function (row) {
                    return {
                        id: String(row.id ?? row.value ?? ''),
                        name: String(row.name ?? row.label ?? row.title ?? ''),
                    };
                }).filter(function (row) {
                    return row.id !== '' && row.name !== '';
                });
            }

            function fillSelect(select, options, selectedId) {
                var placeholder = select.querySelector('option');
                select.innerHTML = '';
                if (placeholder) {
                    select.appendChild(placeholder);
                }
                options.forEach(function (option) {
                    var el = document.createElement('option');
                    el.value = option.id;
                    el.textContent = option.name;
                    if (String(selectedId) === option.id) {
                        el.selected = true;
                    }
                    select.appendChild(el);
                });
            }

            function fetchOptions(url, params) {
                var query = new URLSearchParams(params || {}).toString();
                var target = query ? url + (url.indexOf('?') >= 0 ? '&' : '?') + query : url;

                return fetch(target, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                }).then(function (response) {
                    return response.json();
                }).then(normalizeOptions).catch(function () {
                    return [];
                });
            }

            function syncHiddenName(select, hiddenInput) {
                if (!hiddenInput) {
                    return;
                }
                var option = select.options[select.selectedIndex];
                hiddenInput.value = option && option.value ? option.textContent : '';
            }

            citySelect.addEventListener('change', function () {
                syncHiddenName(citySelect, cityNameInput);
                if (districtSelect) {
                    districtSelect.innerHTML = '<option value=""></option>';
                    districtSelect.disabled = true;
                }
                if (neighborhoodSelect) {
                    neighborhoodSelect.innerHTML = '<option value=""></option>';
                    neighborhoodSelect.disabled = true;
                }
                if (districtNameInput) {
                    districtNameInput.value = '';
                }
                if (neighborhoodNameInput) {
                    neighborhoodNameInput.value = '';
                }
                if (!citySelect.value || !api.districts || !districtSelect) {
                    return;
                }
                fetchOptions(api.districts, { city_id: citySelect.value }).then(function (options) {
                    fillSelect(districtSelect, options, '');
                    districtSelect.disabled = options.length === 0;
                });
            });

            if (districtSelect) {
                districtSelect.addEventListener('change', function () {
                    syncHiddenName(districtSelect, districtNameInput);
                    if (neighborhoodSelect) {
                        neighborhoodSelect.innerHTML = '<option value=""></option>';
                        neighborhoodSelect.disabled = true;
                    }
                    if (neighborhoodNameInput) {
                        neighborhoodNameInput.value = '';
                    }
                    if (!districtSelect.value || !api.neighborhoods || !neighborhoodSelect) {
                        return;
                    }
                    fetchOptions(api.neighborhoods, { district_id: districtSelect.value }).then(function (options) {
                        fillSelect(neighborhoodSelect, options, '');
                        neighborhoodSelect.disabled = options.length === 0;
                    });
                });
            }

            if (neighborhoodSelect) {
                neighborhoodSelect.addEventListener('change', function () {
                    syncHiddenName(neighborhoodSelect, neighborhoodNameInput);
                });
            }

            fetchOptions(api.cities).then(function (options) {
                fillSelect(citySelect, options, initial.city_id || '');
                syncHiddenName(citySelect, cityNameInput);

                if (!initial.city_id || !api.districts || !districtSelect) {
                    return;
                }

                return fetchOptions(api.districts, { city_id: String(initial.city_id) }).then(function (districtOptions) {
                    fillSelect(districtSelect, districtOptions, initial.district_id || '');
                    districtSelect.disabled = districtOptions.length === 0;
                    syncHiddenName(districtSelect, districtNameInput);

                    if (!initial.district_id || !api.neighborhoods || !neighborhoodSelect) {
                        return;
                    }

                    return fetchOptions(api.neighborhoods, { district_id: String(initial.district_id) }).then(function (neighborhoodOptions) {
                        fillSelect(neighborhoodSelect, neighborhoodOptions, initial.neighborhood_id || '');
                        neighborhoodSelect.disabled = neighborhoodOptions.length === 0;
                        syncHiddenName(neighborhoodSelect, neighborhoodNameInput);
                    });
                });
            });
        });
    }
})();
