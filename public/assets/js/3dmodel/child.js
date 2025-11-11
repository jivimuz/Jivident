  (function() {
            // --- Globals ---
            const ORIGINAL_HEX = 0xababab;
            const MARKER_HEX = 0x13293d;

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
                'T_0': '(Left Right) Molar',
                'Gums_Maxilla001_0': '(Top Tube) Gums Maxilla',
                'Gums_Mandibula001_0': '(Bottom Tube) Gums Mandibula',
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
                const loader = new THREE.BINLoader();
                const url = '/assets/bin/ip1.bin.gz'; // ensure placed under public/assets/3d/

                try {
                    const bin = await loader.loadAsync(url);
                    const model = bin.scene;
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
                    document.getElementById('download-pdf').disabled = true;
                    return;
                }

               mappedPoints.slice().reverse().forEach((pt, idx) => {
                    const li = document.createElement('div');
                    li.className =
                        'card mb-3 shadow-sm border-start border-4 border-primary';
                    li.innerHTML = `
                    <div class='row'>
                        <div class="col-md-12">
                            <div class="float-end p-2">
                                    <button class="btn btn-sm rounded-pill btn-outline-danger delete-btn " title="Hapus titik">✖</button>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="card-body  justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="card-title text-primary fw-bold mb-2">${pt.toothId}</h6>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold small text-secondary">Diagnosis:</label>
                                        <select class="form-select form-select-sm diagnosis-select" id="pt-${pt.id}">
                                        <option value="" ${!pt.type ? 'selected' : ''}>Pilih (Opsional)</option>
                                        <option value="Karies" ${pt.type === 'Karies' ? 'selected' : ''}>Karies</option>
                                        <option value="Calculus" ${pt.type === 'Calculus' ? 'selected' : ''}>Calculus</option>
                                        <option value="Fraktur" ${pt.type === 'Fraktur' ? 'selected' : ''}>Fraktur</option>
                                        <option value="Abfraksi" ${pt.type === 'Abfraksi' ? 'selected' : ''}>Abfraksi</option>
                                        <option value="Gingivitis" ${pt.type === 'Gingivitis' ? 'selected' : ''}>Gingivitis</option>
                                        <option value="Pulpitis" ${pt.type === 'Pulpitis' ? 'selected' : ''}>Pulpitis</option>
                                        </select>
                                    </div>
                                    <p class="text-muted small mb-2">Koordinat: (${pt.x}, ${pt.y}, ${pt.z})</p>
                                    <textarea class="form-control form-control-sm" rows="2" placeholder="Tambahkan deskripsi...">${pt.description || ''}</textarea>
                                </div>
                            </div>
                        </div>
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

                document.getElementById('download-pdf').disabled = false;
            }

            async function loadMarker(jsonString) {
                try {
                    loadingOverlay.classList.remove('hidden');

                    let data;
                    try {
                        data = JSON.parse(jsonString);
                    } catch (e) {
                        console.error('Format JSON tidak valid:', e);
                        showToast('Data marker bukan JSON valid', 'error');
                        return;
                    }

                    if (!Array.isArray(data) || data.length === 0) {
                        showToast('Tidak ada marker tersimpan', 'warning');
                        return;
                    }

                    resetMapping();

                    const meshesArray = Array.isArray(toothMeshes) ? toothMeshes : Array.from(toothMeshes);

                    data.forEach(item => {
                        const color = DIAGNOSIS_COLOR_MAP[item.type] || MARKER_HEX;

                        // Buat marker di posisi 3D
                        const geom = new THREE.SphereGeometry(0.05, 8, 8);
                        const mat = new THREE.MeshBasicMaterial({ color });
                        const marker = new THREE.Mesh(geom, mat);
                        marker.position.set(item.x, item.y, item.z);
                        marker.name = 'Marker-' + item.id;
                        scene.add(marker);

                        // Simpan ke mappedPoints
                        mappedPoints.push({
                            id: item.id,
                            toothId: item.toothId,
                            x: item.x,
                            y: item.y,
                            z: item.z,
                            type: item.type || '',
                            description: item.description || ''
                        });

                        // Cari gigi target
                        const targetTooth = meshesArray.find(m =>
                            m?.userData?.parentGroup?.userData?.toothId === item.toothId
                        );

                        if (targetTooth && targetTooth.userData?.parentGroup) {
                            const group = targetTooth.userData.parentGroup;
                            group.traverse(ch => {
                                if (ch.isMesh && ch.material) ch.material.color.setHex(color);
                            });
                            group.userData.isMapped = true;
                            group.userData.markerIds = [item.id];
                        } else {
                            console.warn('Gigi tidak ditemukan untuk toothId:', item.toothId);
                        }
                    });

                    renderPointsList();
                    showToast('Marker berhasil dimuat', 'success');
                } catch (err) {
                    console.error(err);
                    showToast('Gagal memuat marker', 'error');
                } finally {
                    loadingOverlay.classList.add('hidden');
                }
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
                const newId = parent.id;
                console.log(newId)
                parent.traverse(ch => {
                    if (ch.isMesh) ch.material.color.setHex( MARKER_HEX);
                });
                parent.userData.isMapped = true;
                parent.userData.markerIds = [newId];

                const color = MARKER_HEX;
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



            // Wire UI events
            function wireUI() {
                canvas.addEventListener('pointerdown', onCanvasClick);
                document.getElementById('reset-mapping').addEventListener('click', resetMapping);
                document.getElementById('download-pdf').addEventListener('click', downloadPDF);
            }

            // Boot
            async function boot() {
                initThree();
                wireUI();
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
                getMapped: () => mappedPoints,
                loadMarker
            };

        })();
