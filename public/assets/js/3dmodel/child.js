  (function() {
            // --- Globals ---
            const HIGHLIGHT_HEX = 0x3b82f6;
            const ORIGINAL_HEX = 0xababab;
            const MARKER_HEX = 0xff0000;

            let scene, camera, renderer, controls;
            let raycaster = new THREE.Raycaster();
            let pointer = new THREE.Vector2();
            let toothMeshes = [];
            let mappedPoints = []; // {id, toothId, x,y,z,type,description}

            const canvas = document.getElementById('glcanvas');
            const loadingOverlay = document.getElementById('loading-overlay');

            // Simple mapping name map (adjust to your model)
            const TOOTH_MESH_NAME_MAP = {
                'A_0': '(Left Top) Molar',
                'B_0': '(Left Top) Premolar',
                'C_0': '(Left Top) Canine',
                'D_0': '(Left Top) Lateral',
                'E_0': '(Left Top) Central',
                'F_0': '(Right Top) Central',
                'G_0': '(Right Top) Lateral',
                'H_0': '(Right Top) Canine',
                'I_0': '(Right Top) Premolar',
                'J_0': '(Right Top) Molar',
                'K_0': '(Left Bottom) Molar',
                'L_0': '(Left Bottom) Premolar',
                'M_0': '(Left Bottom) Canine',
                'N_0': '(Left Bottom) Lateral',
                'O_0': '(Left Bottom) Central',
                'P_0': '(Left Right) Central',
                'Q_0': '(Left Right) Lateral',
                'R_0': '(Left Right) Canine',
                'S_0': '(Left Right) Premolar',
                'T_0': '(Left Right) Molar'
            };

            function showToast(message, type = 'success') {
                const toast = document.getElementById('toast');
                toast.textContent = message;
                toast.style.backgroundColor = type === 'success' ? '#16a34a' : '#dc2626';
                toast.classList.remove('hidden');
                setTimeout(() => toast.classList.add('hidden'), 3000);
            }

            // Init Three
            function initThree() {
                if (scene) return;
                scene = new THREE.Scene();
                const container = canvas.parentElement;
                const w = container.clientWidth;
                const h = container.clientHeight;

                camera = new THREE.PerspectiveCamera(50, w / h, 0.1, 200);
                camera.position.set(0, 2.5, 130);

                renderer = new THREE.WebGLRenderer({
                    canvas,
                    antialias: true,
                    alpha: true
                });
                renderer.setSize(w, h);
                renderer.setPixelRatio(window.devicePixelRatio || 1);

                controls = new THREE.OrbitControls(camera, renderer.domElement);
                controls.enableDamping = true;
                controls.dampingFactor = 0.05;
                controls.screenSpacePanning = false;
                controls.minDistance = 130;
                controls.maxDistance = 160;
                controls.target.set(4, 5, 0);

                const ambient = new THREE.AmbientLight(0xffffff, 0.8);
                scene.add(ambient);
                const key = new THREE.DirectionalLight(0xffffff, 0.6);
                key.position.set(10, 10, 10);
                scene.add(key);
                const fill = new THREE.DirectionalLight(0xffffff, 0.4);
                fill.position.set(-10, -10, -5);
                scene.add(fill);

                function animate() {
                    requestAnimationFrame(animate);
                    controls.update();
                    renderer.render(scene, camera);
                }
                animate();

                window.addEventListener('resize', () => {
                    const c = canvas.parentElement;
                    if (!c) return;
                    camera.aspect = c.clientWidth / c.clientHeight;
                    camera.updateProjectionMatrix();
                    renderer.setSize(c.clientWidth, c.clientHeight);
                });
            }

            async function loadModel() {
                loadingOverlay.classList.remove('hidden');
                // cleanup old
                const old = scene.getObjectByName('ArchGroup');
                if (old) {
                    scene.remove(old);
                }

                toothMeshes = [];
                const loader = new THREE.GLTFLoader();
                const url = '/assets/3d/ip2.bin.gz'; // ensure placed under public/assets/3d/

                try {
                    const gltf = await loader.loadAsync(url);
                    const model = gltf.scene;
                    model.name = 'ArchGroup';
                    model.scale.set(1, 1, 1);
                    model.position.set(0, -2, 0);
                    model.rotation.set(0, 0, 0);

                    const defaultMaterial = new THREE.MeshPhongMaterial({
                        color: ORIGINAL_HEX,
                        specular: 0x555555,
                        shininess: 30,
                        side: THREE.DoubleSide
                    });

                    model.traverse((child) => {
                        if (child.isMesh) {
                            const mapped = TOOTH_MESH_NAME_MAP[child.name] || child.name;
                            const mat = defaultMaterial.clone();
                            child.material = mat;

                            const parent = child.parent && child.parent.type !== 'Scene' ? child.parent :
                                child;
                            parent.userData = parent.userData || {};
                            if (!parent.userData.isTooth) {
                                parent.userData.isTooth = true;
                                parent.userData.toothId = mapped;
                                parent.userData.isMapped = false;
                                parent.userData.markerIds = [];
                            }
                            child.userData.parentGroup = parent;
                            toothMeshes.push(child);
                        }
                    });

                    scene.add(model);
                    showToast('3D model loaded', 'success');
                } catch (err) {
                    console.error('loadModel error', err);
                    showToast('Failed to load model', 'error');
                } finally {
                    loadingOverlay.classList.add('hidden');
                }
            }

            // Remove markers helper
            function removeMarkersFromScene(markerIds) {
                markerIds.forEach(id => {
                    const m = scene.getObjectByName('Marker-' + id);
                    if (m) {
                        scene.remove(m);
                        if (m.geometry) m.geometry.dispose();
                        if (m.material) m.material.dispose();
                    }
                });
            }

            // Update UI list
            function renderPointsList() {
                const el = document.getElementById('points-list');
                el.innerHTML = '';
                document.getElementById('points-count').textContent = `(${mappedPoints.length})`;

                if (mappedPoints.length === 0) {
                    el.innerHTML = '<p class="text-gray-500 italic text-sm">No points have been marked yet.</p>';
                    document.getElementById('save-json').disabled = true;
                    document.getElementById('download-pdf').disabled = true;
                    return;
                }

                mappedPoints.slice().reverse().forEach((pt, idx) => {
                    const li = document.createElement('div');
                    li.className =
                        'bg-white p-3 rounded-lg shadow-sm border-l-4 border-blue-500 mb-2 flex justify-between items-start';
                    li.innerHTML = `
          <div class="flex-1">
            <div class="font-bold text-lg text-blue-600">${pt.toothId}</div>
            <div class="font-medium text-gray-800 text-sm">- ${pt.type || 'Marker'} #${mappedPoints.length - idx}</div>
            <p class="text-xs text-gray-600 mt-1">Coordinates: (${pt.x}, ${pt.y}, ${pt.z})</p>
            <textarea class="w-full mt-2 p-2 border border-gray-300 rounded-md text-sm text-gray-700 resize-none" rows="2" placeholder="Add description...">${pt.description || ''}</textarea>
          </div>
          <div class="ml-2 flex flex-col items-center">
            <button class="delete-btn p-1 rounded-full text-gray-400 hover:bg-red-100 hover:text-red-600 mb-2">✖</button>
          </div>
        `;
                    const textarea = li.querySelector('textarea');
                    textarea.addEventListener('input', (e) => {
                        pt.description = e.target.value.slice(0, 200);
                    });
                    const del = li.querySelector('.delete-btn');
                    del.addEventListener('click', () => {
                        deletePoint(pt.id);
                    });
                    el.appendChild(li);
                });

                document.getElementById('save-json').disabled = false;
                document.getElementById('download-pdf').disabled = false;
            }

            function deletePoint(id) {
                const pt = mappedPoints.find(p => p.id === id);
                if (!pt) return;
                const toothId = pt.toothId;
                removeMarkersFromScene([id]);
                mappedPoints = mappedPoints.filter(p => p.id !== id);

                // if no other points for this tooth, reset color
                const stillMapped = mappedPoints.some(p => p.toothId === toothId);
                if (!stillMapped) {
                    const groupMesh = toothMeshes.find(m => m.userData.parentGroup && m.userData.parentGroup.userData
                        .toothId === toothId)?.userData.parentGroup;
                    if (groupMesh) {
                        groupMesh.traverse(ch => {
                            if (ch.isMesh) ch.material.color.setHex(ORIGINAL_HEX);
                        });
                        groupMesh.userData.isMapped = false;
                        groupMesh.userData.markerIds = [];
                    }
                }
                renderPointsList();
                showToast('Point deleted', 'success');
            }

            // Canvas click handler
            function onCanvasClick(evt) {
                const rect = canvas.getBoundingClientRect();
                pointer.x = ((evt.clientX - rect.left) / rect.width) * 2 - 1;
                pointer.y = -((evt.clientY - rect.top) / rect.height) * 2 + 1;

                raycaster.setFromCamera(pointer, camera);
                const intersects = raycaster.intersectObjects(toothMeshes, false);
                if (intersects.length === 0) return;
                const inter = intersects[0];
                const clickedMesh = inter.object;
                const parent = clickedMesh.userData.parentGroup;
                if (!parent || !parent.userData || !parent.userData.isTooth) return;

                const toothId = parent.userData.toothId;
                const point = inter.point;
                const isMapped = parent.userData.isMapped;

                if (isMapped) {
                    // remove
                    const ids = parent.userData.markerIds || [];
                    removeMarkersFromScene(ids);
                    parent.traverse(ch => {
                        if (ch.isMesh) ch.material.color.setHex(ORIGINAL_HEX);
                    });
                    parent.userData.isMapped = false;
                    parent.userData.markerIds = [];
                    mappedPoints = mappedPoints.filter(p => p.toothId !== toothId);
                    renderPointsList();
                    showToast('Marker removed', 'success');
                    return;
                }

                // add
                const newId = Date.now();
                parent.traverse(ch => {
                    if (ch.isMesh) ch.material.color.setHex(HIGHLIGHT_HEX);
                });
                parent.userData.isMapped = true;
                parent.userData.markerIds = [newId];

                const geom = new THREE.SphereGeometry(0.05, 8, 8);
                const mat = new THREE.MeshBasicMaterial({
                    color: MARKER_HEX
                });
                const marker = new THREE.Mesh(geom, mat);
                marker.position.copy(point);
                marker.name = 'Marker-' + newId;
                scene.add(marker);

                mappedPoints.push({
                    id: newId,
                    toothId: toothId,
                    x: point.x.toFixed(2),
                    y: point.y.toFixed(2),
                    z: point.z.toFixed(2),
                    type: 'Karies',
                    description: ''
                });
                renderPointsList();
                showToast('Marker added', 'success');
            }

            // Reset mapping
            function resetMapping() {
                const markers = scene.children.filter(c => c.name && c.name.startsWith('Marker-'));
                markers.forEach(m => {
                    scene.remove(m);
                    if (m.geometry) m.geometry.dispose();
                    if (m.material) m.material.dispose();
                });
                mappedPoints = [];
                toothMeshes.forEach(m => {
                    if (m.material) m.material.color.setHex(ORIGINAL_HEX);
                    if (m.userData.parentGroup) {
                        m.userData.parentGroup.userData.isMapped = false;
                        m.userData.parentGroup.userData.markerIds = [];
                    }
                });
                renderPointsList();
                showToast('All mapping reset', 'success');
            }

            // Save JSON to clipboard
            function saveJSON() {
                const data = {
                    mappedPoints,
                    timestamp: new Date().toISOString()
                };
                const json = JSON.stringify(data, null, 2);
                // copy fallback
                const ta = document.createElement('textarea');
                ta.value = json;
                document.body.appendChild(ta);
                ta.select();
                try {
                    document.execCommand('copy');
                    showToast('JSON copied to clipboard', 'success');
                } catch (err) {
                    showToast('Copy failed', 'error');
                }
                document.body.removeChild(ta);
            }

            // Download PDF using jsPDF
            function downloadPDF() {
                if (!window.jspdf) {
                    showToast('PDF library not ready', 'error');
                    return;
                }
                const {
                    jsPDF
                } = window.jspdf;
                const doc = new jsPDF('p', 'mm', 'a4');
                const pageW = doc.internal.pageSize.getWidth();
                const margin = 15;
                let y = 20;

                doc.setFontSize(18);
                doc.text('Odontogram Diagnostic Report', pageW / 2, y, {
                    align: 'center'
                });
                y += 8;
                doc.setLineWidth(0.5);
                doc.line(margin, y, pageW - margin, y);
                y += 8;
                doc.setFontSize(11);
                doc.text('Patient Name: API Patient (Placeholder)', margin, y);
                doc.text('ID Card: ID-001-API', pageW - 80, y);
                y += 8;
                doc.text('Date Printed: ' + new Date().toLocaleDateString('en-US'), margin, y);
                y += 10;

                if (mappedPoints.length === 0) {
                    doc.setFontSize(11);
                    doc.text('No diagnostic points have been marked.', margin, y);
                    doc.save('Odontogram_Report');
                    return;
                }

                mappedPoints.forEach((pt, idx) => {
                    if (y > 270) {
                        doc.addPage();
                        y = 20;
                    }
                    doc.setFontSize(12);
                    doc.text(`${pt.toothId} (${pt.type})`, margin, y);
                    y += 6;
                    doc.setFontSize(10);
                    doc.text(`Coords: (${pt.x}, ${pt.y}, ${pt.z})`, margin + 2, y);
                    y += 6;
                    doc.setFontSize(10);
                    doc.text('Description: ' + (pt.description || '(No description)'), margin + 2, y);
                    y += 10;
                });

                const stamp = new Date();
                const fname =
                    `Odontogram_Report_${stamp.getFullYear()}${String(stamp.getMonth()+1).padStart(2,'0')}${String(stamp.getDate()).padStart(2,'0')}_${String(stamp.getHours()).padStart(2,'0')}${String(stamp.getMinutes()).padStart(2,'0')}`;
                doc.save(fname);
                showToast('PDF saved', 'success');
            }

            // fetch IP
            async function fetchIP() {
                try {
                    const r = await fetch('https://api.ipify.org?format=json');
                    const d = await r.json();
                    document.getElementById('user-ip').textContent = d.ip || 'unknown';
                } catch (e) {
                    document.getElementById('user-ip').textContent = 'unavailable';
                }
            }

            // Wire UI events
            function wireUI() {
                canvas.addEventListener('pointerdown', onCanvasClick);
                document.getElementById('reset-mapping').addEventListener('click', resetMapping);
                document.getElementById('save-json').addEventListener('click', saveJSON);
                document.getElementById('download-pdf').addEventListener('click', downloadPDF);
            }

            // Boot
            async function boot() {
                initThree();
                wireUI();
                fetchIP();
                await loadModel();
                renderPointsList();
            }

            // Start when DOM ready
            if (document.readyState === 'complete' || document.readyState === 'interactive') {
                setTimeout(boot, 50);
            } else document.addEventListener('DOMContentLoaded', boot);

            // Expose for debugging
            window._jivident = {
                sceneGetter: () => scene,
                getMapped: () => mappedPoints
            };

        })();
