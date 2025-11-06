  (function() {
            // --- Globals ---
            const ORIGINAL_HEX = 0xababab;
            const MARKER_HEX = 0xff0000;

            let scene, camera, renderer, controls;
            let raycaster = new THREE.Raycaster();
            let pointer = new THREE.Vector2();
            let toothMeshes = [];
            let mappedPoints = []; // {id, toothId, x,y,z,type,description}
            let markerIdCounter = 0;
            const canvas = document.getElementById('glcanvas');
            const loadingOverlay = document.getElementById('loading-overlay');

            // Simple mapping name map (adjust to your model)
            // Simple mapping name map (adjust to your model)
          const TOOTH_MESH_NAME_MAP = {
            // --- Rahang Atas Kanan (Quadrant 1) ---
            "Box023_01_-_Default_0": "11 (Right Top) Central Incisor",
            "Box024_01_-_Default_0": "12 (Right Top) Lateral Incisor",
            "Box025_01_-_Default_0": "13 (Right Top) Canine",
            "Box031_01_-_Default_0": "14 (Right Top) First Premolar",
            "Box033_01_-_Default_0": "15 (Right Top) Second Premolar",
            "Box032_01_-_Default_0": "16 (Right Top) First Molar",
            "Box035_01_-_Default_0": "17 (Right Top) Second Molar",
            "Box034_01_-_Default_0": "18 (Right Top) Third Molar",

            // --- Rahang Atas Kiri (Quadrant 2) ---
            "Box020_01_-_Default_0": "21 (Left Top) Central Incisor",
            "Box021_01_-_Default_0": "22 (Left Top) Lateral Incisor",
            "Box022_01_-_Default_0": "23 (Left Top) Canine",
            "Box026_01_-_Default_0": "24 (Left Top) First Premolar",
            "Box028_01_-_Default_0": "25 (Left Top) Second Premolar",
            "Box027_01_-_Default_0": "26 (Left Top) First Molar",
            "Box030_01_-_Default_0": "27 (Left Top) Second Molar",
            "Box029_01_-_Default_0": "28 (Left Top) Third Molar",

                    // --- Rahang Bawah Kiri (Quadrant 3) ---
            'Box011_01_-_Default_0': '38 (Left Bottom) Third Molar',
            'Box013_01_-_Default_0': '37 (Left Bottom) Second Molar',
            'Box008_01_-_Default_0': '36 (Left Bottom) First Molar',
            'Box009_01_-_Default_0': '35 (Left Bottom) Second Premolar',
            'Box007_01_-_Default_0': '34 (Left Bottom) First Premolar',
            'Box003_01_-_Default_0': '33 (Left Bottom) Canine',
            'Box002_01_-_Default_0': '32 (Left Bottom) Lateral Incisor',
            'Box001_01_-_Default_0': '31 (Left Bottom) Central Incisor',

            // --- Rahang Bawah Kanan (Quadrant 4) ---
            'Box004_01_-_Default_0': '41 (Right Bottom) Central Incisor',
            'Box005_01_-_Default_0': '42 (Right Bottom) Lateral Incisor',
            'Box006_01_-_Default_0': '43 (Right Bottom) Canine',
            'Box015_01_-_Default_0': '44 (Right Bottom) First Premolar',
            'Box017_01_-_Default_0': '45 (Right Bottom) Second Premolar',
            'Box016_01_-_Default_0': '46 (Right Bottom) First Molar',
            'Box019_01_-_Default_0': '47 (Right Bottom) Second Molar',
            'GeoSphere001_01_-_Default_0': '48 (Right Bottom) Third Molar',

            // --- Lain-lain ---
            'Tube002_07_-_Default_0': '(Top Tube) Gums Maxilla',
            'Tube001_07_-_Default_0': '(Bottom Tube) Mandibula',
            'lingua_02_-_Default_0': '(Tongue) Lingua'
            };

            const DIAGNOSIS_COLOR_MAP = {
            'Karies': 0x3b82f6,
            'Calculus': 0xbfb467,
            'Fraktur': 0x59909c,
            'Abfraksi': 0x9c4f6f,
            'Gingivitis': 0x9c784f,
            'Pulpitis': 0x9c504f
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
                document.addEventListener('change', function (e) {
                if (!e.target.classList.contains('diagnosis-select')) return;

                const select = e.target;
                const idStr = select.id.replace('pt-', '');
                const id = parseInt(idStr, 10);
                const pt = mappedPoints.find(p => p.id === id);
                if (!pt) return;

                // update data
                pt.type = select.value;

                // warna berdasarkan diagnosis (fallback ke MARKER_HEX kalau undefined)
                const color = DIAGNOSIS_COLOR_MAP[pt.type] || MARKER_HEX;

                // 1) update marker color (jika ada)
                const marker = scene.getObjectByName('Marker-' + id);
                if (marker && marker.material) {
                    marker.material.color.setHex(color);
                }

                // 2) update the tooth *parent group* color (ubah seluruh mesh di dalam parent)
                // cari parent group lewat toothMeshes (child meshes menyimpan parentGroup)
                let parentGroup = null;
                for (let i = 0; i < toothMeshes.length; i++) {
                    const ch = toothMeshes[i];
                    if (ch.userData && ch.userData.parentGroup && ch.userData.parentGroup.userData) {
                    if (ch.userData.parentGroup.userData.toothId === pt.toothId) {
                        parentGroup = ch.userData.parentGroup;
                        break;
                    }
                    }
                }

                if (parentGroup) {
                    parentGroup.traverse(obj => {
                    if (obj.isMesh && obj.material) {
                        obj.material.color.setHex(color);
                    }
                    });
                    parentGroup.userData.isMapped = true;
                } else {
                    console.warn('Parent group not found for toothId', pt.toothId);
                }

                showToast(`Diagnosis updated to ${pt.type}`, 'success');
                });

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
              <div class="flex items-center gap-2 mb-2">
                <label class="text-sm font-medium text-gray-700">Diagnosis:</label>
                <select class="diagnosis-select border border-gray-300 rounded-md p-1 text-sm" id='pt-${pt.id}'>
                <option value="" ${!pt.type ? 'selected' : ''}>Pilih (Opsional)</option>
                <option value="Karies" ${pt.type === 'Karies' ? 'selected' : ''}>Karies</option>
                <option value="Calculus" ${pt.type === 'Calculus' ? 'selected' : ''}>Calculus</option>
                <option value="Fraktur" ${pt.type === 'Fraktur' ? 'selected' : ''}>Fraktur</option>
                <option value="Abfraksi" ${pt.type === 'Abfraksi' ? 'selected' : ''}>Abfraksi</option>
                <option value="Gingivitis" ${pt.type === 'Gingivitis' ? 'selected' : ''}>Gingivitis</option>
                <option value="Pulpitis" ${pt.type === 'Pulpitis' ? 'selected' : ''}>Pulpitis</option>
                </select>
            </div>
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
                    if (ch.isMesh) ch.material.color.setHex(DIAGNOSIS_COLOR_MAP['Karies'] || MARKER_HEX);
                });
                parent.userData.isMapped = true;
                parent.userData.markerIds = [newId];

                const color = DIAGNOSIS_COLOR_MAP['Karies'] || MARKER_HEX;
                const geom = new THREE.SphereGeometry(0.05, 8, 8);
                const mat = new THREE.MeshBasicMaterial({ color });
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
                    type: '',
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
