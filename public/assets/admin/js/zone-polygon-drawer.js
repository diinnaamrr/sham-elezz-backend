/**
 * Replacement for deprecated google.maps.drawing.DrawingManager (polygon mode only).
 * Google removed the Drawing Library in May 2026.
 */
(function () {
    'use strict';

    const POLYGON = 'polygon';

    function ZonePolygonDrawer(options) {
        options = options || {};
        let map = null;
        let isDrawing = false;
        let path = [];
        let previewLine = null;
        let completeHandlers = [];
        let clickListener = null;
        let dblClickListener = null;

        const controlPosition =
            (options.drawingControlOptions && options.drawingControlOptions.position) ||
            (typeof google !== 'undefined' && google.maps
                ? google.maps.ControlPosition.TOP_CENTER
                : 0);

        const polygonDefaults = Object.assign(
            {
                editable: true,
                strokeColor: '#1a73e8',
                strokeOpacity: 1,
                strokeWeight: 2,
                fillColor: '#1a73e8',
                fillOpacity: 0.35,
            },
            options.polygonOptions || {}
        );

        const control = buildControl();

        function buildControl() {
            const container = document.createElement('div');
            container.className = 'zone-drawing-control';
            container.style.cssText =
                'display:flex;background:#fff;box-shadow:rgba(0,0,0,.3) 0 1px 4px -1px;border-radius:2px;margin:10px 5px;';

            const handBtn = makeButton(
                'M9 11V8l4-4 4 4v3h2v8H7v-8h2zm2 0h6V9.17L13 6.34 11 9.17V11z',
                'Pan map'
            );
            const polyBtn = makeButton(
                'M14 6l-3.75 5 2.85 3.8-1.6 1.2C9.81 13.75 7 10 7 10l-4 5h18l-6-9z',
                'Draw zone shape',
                true
            );

            handBtn.addEventListener('click', disableDrawing);
            polyBtn.addEventListener('click', enableDrawing);

            container.appendChild(handBtn);
            container.appendChild(polyBtn);

            return { container, handBtn, polyBtn };
        }

        function makeButton(pathD, title, active) {
            const btn = document.createElement('div');
            btn.title = title;
            btn.style.cssText =
                'width:40px;height:40px;display:flex;align-items:center;justify-content:center;cursor:pointer;background:' +
                (active ? '#e8e8e8' : '#fff') +
                ';border-right:1px solid #e0e0e0;';
            btn.innerHTML =
                '<svg width="18" height="18" viewBox="0 0 24 24" fill="#666"><path d="' +
                pathD +
                '"/></svg>';
            return btn;
        }

        function setActiveButton(drawing) {
            control.handBtn.style.background = drawing ? '#fff' : '#e8e8e8';
            control.polyBtn.style.background = drawing ? '#e8e8e8' : '#fff';
        }

        function enableDrawing() {
            if (!map || isDrawing) return;
            isDrawing = true;
            path = [];
            clearPreview();
            setActiveButton(true);
            map.setOptions({
                draggableCursor: 'crosshair',
                draggingCursor: 'crosshair',
                disableDoubleClickZoom: true,
            });
            clickListener = map.addListener('click', onMapClick);
            dblClickListener = map.addListener('dblclick', onMapDoubleClick);
        }

        function disableDrawing() {
            if (!map) return;
            isDrawing = false;
            path = [];
            clearPreview();
            setActiveButton(false);
            map.setOptions({
                draggableCursor: null,
                draggingCursor: null,
                disableDoubleClickZoom: false,
            });
            if (clickListener) {
                google.maps.event.removeListener(clickListener);
                clickListener = null;
            }
            if (dblClickListener) {
                google.maps.event.removeListener(dblClickListener);
                dblClickListener = null;
            }
        }

        function onMapClick(event) {
            path.push(event.latLng);
            updatePreview();

            if (path.length >= 3 && isNear(path[0], event.latLng)) {
                finishPolygon();
            }
        }

        function onMapDoubleClick(event) {
            event.stop();
            if (path.length >= 3) {
                finishPolygon();
            }
        }

        function isNear(a, b) {
            return (
                Math.abs(a.lat() - b.lat()) < 0.0003 &&
                Math.abs(a.lng() - b.lng()) < 0.0003
            );
        }

        function finishPolygon() {
            if (path.length < 3) return;

            clearPreview();
            const polygon = new google.maps.Polygon(
                Object.assign({}, polygonDefaults, {
                    paths: path.slice(),
                    map: map,
                })
            );

            disableDrawing();

            const event = {
                overlay: polygon,
                type: POLYGON,
            };
            completeHandlers.forEach(function (handler) {
                handler(event);
            });
        }

        function updatePreview() {
            if (previewLine) {
                previewLine.setMap(null);
            }
            if (path.length >= 2) {
                previewLine = new google.maps.Polyline({
                    path: path,
                    strokeColor: polygonDefaults.strokeColor,
                    strokeOpacity: polygonDefaults.strokeOpacity,
                    strokeWeight: polygonDefaults.strokeWeight,
                    map: map,
                });
            }
        }

        function clearPreview() {
            if (previewLine) {
                previewLine.setMap(null);
                previewLine = null;
            }
        }

        function shouldStartDrawing() {
            const mode = options.drawingMode;
            return (
                mode === POLYGON ||
                (typeof google !== 'undefined' &&
                    google.maps &&
                    google.maps.drawing &&
                    mode === google.maps.drawing.OverlayType.POLYGON)
            );
        }

        return {
            setMap: function (targetMap) {
                map = targetMap;
                if (!map) {
                    disableDrawing();
                    return;
                }
                if (options.drawingControl !== false) {
                    map.controls[controlPosition].push(control.container);
                }
                if (shouldStartDrawing()) {
                    enableDrawing();
                }
            },
            addListener: function (event, handler) {
                if (event === 'overlaycomplete') {
                    completeHandlers.push(handler);
                }
                return {
                    remove: function () {
                        completeHandlers = completeHandlers.filter(function (h) {
                            return h !== handler;
                        });
                    },
                };
            },
        };
    }

    window.ZonePolygonDrawer = ZonePolygonDrawer;

    if (typeof google !== 'undefined' && google.maps) {
        google.maps.drawing = google.maps.drawing || {};
        google.maps.drawing.OverlayType = google.maps.drawing.OverlayType || {
            POLYGON: POLYGON,
        };
        google.maps.drawing.DrawingManager = ZonePolygonDrawer;
    }
})();
