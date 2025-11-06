<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Jivident — 3D Odontogram (Standalone HTML)</title>
    <meta name="description" content="3D odontogram mapping demo (plain HTML + Three.js)." />

    <!-- Tailwind CDN (for quick styling) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        html,
        body,
        #app {
            height: 100%;
        }

        .canvas-container {
            height: 560px;
        }

        @media(min-width:1024px) {
            .canvas-container {
                height: 720px;
            }
        }

        .rounded-circle {
            border-radius: 9999px
        }

        textarea:focus {
            outline: none
        }
    </style>
</head>

<body class="bg-gray-50 font-sans">
    <div id="app" class="min-h-screen flex flex-col p-4 lg:p-8">

        <div class="flex flex-col lg:flex-row gap-6 flex-1">
            <!-- 3D Canvas -->
            <div class="canvas-container bg-gray rounded-xl overflow-hidden p-0 lg:p-6 lg:w-3/5">
                <div class="p-4 lg:p-0 flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-teal-700">3D Odontogram Mapping</h2>
                        <p class="text-sm text-gray-500">Click a tooth to add/remove a diagnostic marker.</p>
                    </div>
                    <div class="text-sm text-gray-400">IP: <span id="user-ip">loading...</span></div>
                </div>
                <div class="relative w-full h-full">
                    <canvas id="glcanvas" class="w-full h-full cursor-pointer"></canvas>
                    <div id="loading-overlay"
                        class="absolute inset-0 flex items-center justify-center bg-gray-100/70 z-10 hidden">
                        <div class="text-center">
                            <svg class="animate-spin -ml-1 mr-3 h-8 w-8 text-teal-600"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <p class="text-teal-600 font-semibold">Loading 3D Model...</p>
                        </div>
                    </div>
                    <div
                        class="absolute top-4 right-4 bg-teal-500 text-white text-xs font-semibold px-3 py-1 rounded-full shadow-lg">
                        Click tooth to toggle marker</div>
                </div>
            </div>

            <!-- Control Panel -->
            <div class="lg:w-2/5">
                <div class="bg-white rounded-xl shadow-2xl p-6" style="min-height:80vh">
                    <h2 class="text-2xl font-bold text-gray-800 border-b pb-3 mb-4">Odontology Diagnostic Panel</h2>

                    <div class="mb-6 p-4 bg-teal-50 rounded-lg border border-teal-200">
                        <p class="text-sm font-semibold text-teal-800">Patient: <span id="patient-name"
                                class="font-bold">API Patient (Placeholder)</span></p>
                        <p class="font-mono text-xs text-teal-600 break-words">Session IP: <span
                                id="patient-id">ID-001-API</span></p>
                        <p class="text-gray-500 text-xs mt-2">Data placeholder — replace with your API data.</p>
                    </div>

                    <h3 class="text-xl font-semibold text-gray-700 mb-3">Diagnostic Points <span id="points-count"
                            class="text-sm text-gray-500">(0)</span></h3>
                    <div id="points-list" class="h-96 overflow-y-auto border border-gray-200 rounded-lg p-3 bg-gray-50">
                    </div>

                    <div class="mt-6 space-y-2">
                        <div class="flex flex-col sm:flex-row sm:space-x-4 space-y-2 sm:space-y-0">
                            <button id="save-json"
                                class="w-full sm:w-1/2 py-3 bg-green-600 text-white font-semibold rounded-lg shadow-md hover:bg-green-700 disabled:opacity-50"
                                disabled>Save Diagnostic JSON</button>
                            <button id="download-pdf"
                                class="w-full sm:w-1/2 py-3 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 disabled:opacity-50"
                                disabled>Download Diagnostic PDF</button>
                        </div>
                        <button id="reset-mapping"
                            class="w-full py-3 bg-red-500 text-white font-semibold rounded-lg shadow-md hover:bg-red-600">Reset
                            All Mapping Points</button>
                    </div>

                </div>
            </div>
        </div>

        <!-- Toast -->
        <div id="toast" class="fixed top-6 right-6 z-50 p-4 rounded-lg shadow-xl text-white hidden"></div>

    </div>

    <!-- Three.js core -->
    <script src="{{ url('') }}/assets/js/3dmodel/three.js"></script>
    <script src="{{ url('') }}/assets/js/3dmodel/orbitControl.js"></script>
    <script src="{{ url('') }}/assets/js/3dmodel/gltfloader.js"></script>
    <script src="{{ url('') }}/assets/js/3dmodel/jspdf.js"></script>
    <script src="{{ url('') }}/assets/js/3dmodel/adult.js"></script>

    <script></script>
</body>

</html>
