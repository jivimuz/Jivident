<style>
    .custom-button {
        width: 25px;
        height: 25px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .custom-button svg {
        width: 28px;
        height: 28px;
    }

    .hover:hover {
        fill: lightblue;
        cursor: pointer;
    }

    .nav-link {
        cursor: pointer;
    }
</style>
<!-- Main content -->


<form id="form-odonto">
    <div class="row">
        <br>
        <div class="col-md-8 p-4">
            <h4 class="fw-700">Mapping 2D Global :</h4>

        </div>
        <div class="col-md-4">
            @if ($status->status == 'Di Periksa')
                <br>
                <br>
                <div class="text-center d-flex align-items-center">
                    <div class="col-md-12">
                        <a id='save-data' class="btn btn-sm rounded-pill btn-outline-success"><i class="fa fa-save"></i>
                            Save
                        </a> &nbsp;
                        <a onclick="resetAllGigi()" class="btn rounded-pill btn-sm btn-outline-warning"><i
                                class="fa fa-refresh"></i>
                            Reset</a>
                        <br>
                    </div>

                </div>
            @endif
        </div>
        <br>
    </div>
    <div class="table-responsive">
        <table class="table table-striped table-sm">
            <tbody>
                <tr>
                    <td title="Gigi 18">
                        <div>
                            <h6 class="text-center">18</h6>
                        </div>
                        <div>
                            <div class="custom-button mx-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <g>
                                        <polygon points="0,0 20,0 15,5 5,5" fill="white" class="gigi hover"
                                            accesskey="18T" stroke="navy" stroke-width="0.5" id="v-18T"
                                            opacity="1"></polygon>
                                        <polygon points="5,15 15,15 20,20 0,20" fill="white" class="gigi hover"
                                            accesskey="18B" stroke="navy" stroke-width="0.5" id="v-18B"
                                            opacity="1"></polygon>
                                        <polygon points="15,5 20,0 20,20 15,15" fill="white" class="gigi hover"
                                            accesskey="18R" stroke="navy" stroke-width="0.5" id="v-18R"
                                            opacity="1"></polygon>
                                        <polygon points="0,0 5,5 5,15 0,20" fill="white" class="gigi hover"
                                            accesskey="18L" stroke="navy" stroke-width="0.5" id="v-18L"
                                            opacity="1"></polygon>
                                        <polygon points="5,5 15,5 15,15 5,15" fill="white" class="gigi hover"
                                            accesskey="18C" stroke="navy" stroke-width="0.5" id="v-18C"
                                            opacity="1"></polygon>
                                        <text id="text-18" x="7" y="12" fill="black"
                                            style="font-size: 4pt;font-weight:bold"></text>
                                    </g>
                                </svg>

                            </div>
                            <input type="text" value="0" id="gigi-18T" name="gigi-18T" hidden>
                            <input type="text" value="0" id="gigi-18B" name="gigi-18B" hidden>
                            <input type="text" value="0" id="gigi-18R" name="gigi-18R" hidden>
                            <input type="text" value="0" id="gigi-18L" name="gigi-18L" hidden>
                            <input type="text" value="0" id="gigi-18C" name="gigi-18C" hidden>

                        </div>
                    </td>
                    <td title="Gigi 17">
                        <div>
                            <h6 class="text-center">17</h6>
                        </div>
                        <div>
                            <div>
                                <div class="custom-button mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <g>
                                            <polygon points="0,0 20,0 15,5 5,5" fill="white" class="gigi hover"
                                                accesskey="17T" stroke="navy" stroke-width="0.5" id="v-17T"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,15 15,15 20,20 0,20" fill="white" class="gigi hover"
                                                accesskey="17B" stroke="navy" stroke-width="0.5" id="v-17B"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="15,5 20,0 20,20 15,15" fill="white" class="gigi hover"
                                                accesskey="17R" stroke="navy" stroke-width="0.5" id="v-17R"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="0,0 5,5 5,15 0,20" fill="white" class="gigi hover"
                                                accesskey="17L" stroke="navy" stroke-width="0.5" id="v-17L"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,5 15,5 15,15 5,15" fill="white" class="gigi hover"
                                                accesskey="17C" stroke="navy" stroke-width="0.5" id="v-17C"
                                                opacity="1">
                                            </polygon>
                                            <text id="text-17" x="7" y="12" fill="black"
                                                style="font-size: 4pt;font-weight:bold"></text>
                                        </g>
                                    </svg>

                                </div>
                                <input type="text" value="0" id="gigi-17T" name="gigi-17T" hidden>
                                <input type="text" value="0" id="gigi-17B" name="gigi-17B" hidden>
                                <input type="text" value="0" id="gigi-17R" name="gigi-17R" hidden>
                                <input type="text" value="0" id="gigi-17L" name="gigi-17L" hidden>
                                <input type="text" value="0" id="gigi-17C" name="gigi-17C" hidden>

                            </div>
                        </div>
                    </td>
                    <td title="Gigi 16">
                        <div>
                            <h6 class="text-center">16</h6>
                        </div>
                        <div>
                            <div>
                                <div class="custom-button mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <g>
                                            <polygon points="0,0 20,0 15,5 5,5" fill="white" class="gigi hover"
                                                accesskey="16T" stroke="navy" stroke-width="0.5" id="v-16T"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,15 15,15 20,20 0,20" fill="white" class="gigi hover"
                                                accesskey="16B" stroke="navy" stroke-width="0.5" id="v-16B"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="15,5 20,0 20,20 15,15" fill="white" class="gigi hover"
                                                accesskey="16R" stroke="navy" stroke-width="0.5" id="v-16R"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="0,0 5,5 5,15 0,20" fill="white" class="gigi hover"
                                                accesskey="16L" stroke="navy" stroke-width="0.5" id="v-16L"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,5 15,5 15,15 5,15" fill="white" class="gigi hover"
                                                accesskey="16C" stroke="navy" stroke-width="0.5" id="v-16C"
                                                opacity="1">
                                            </polygon>
                                            <text id="text-16" x="7" y="12" fill="black"
                                                style="font-size: 4pt;font-weight:bold"></text>
                                        </g>
                                    </svg>

                                </div>
                                <input type="text" value="0" id="gigi-16T" name="gigi-16T" hidden>
                                <input type="text" value="0" id="gigi-16B" name="gigi-16B" hidden>
                                <input type="text" value="0" id="gigi-16R" name="gigi-16R" hidden>
                                <input type="text" value="0" id="gigi-16L" name="gigi-16L" hidden>
                                <input type="text" value="0" id="gigi-16C" name="gigi-16C" hidden>

                            </div>
                        </div>
                    </td>
                    <td title="Gigi 15">
                        <div>
                            <h6 class="text-center">15</h6>
                        </div>
                        <div>
                            <div>
                                <div class="custom-button mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <g>
                                            <polygon points="0,0 20,0 15,5 5,5" fill="white" class="gigi hover"
                                                accesskey="15T" stroke="navy" stroke-width="0.5" id="v-15T"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,15 15,15 20,20 0,20" fill="white" class="gigi hover"
                                                accesskey="15B" stroke="navy" stroke-width="0.5" id="v-15B"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="15,5 20,0 20,20 15,15" fill="white" class="gigi hover"
                                                accesskey="15R" stroke="navy" stroke-width="0.5" id="v-15R"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="0,0 5,5 5,15 0,20" fill="white" class="gigi hover"
                                                accesskey="15L" stroke="navy" stroke-width="0.5" id="v-15L"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,5 15,5 15,15 5,15" fill="white" class="gigi hover"
                                                accesskey="15C" stroke="navy" stroke-width="0.5" id="v-15C"
                                                opacity="1">
                                            </polygon>
                                            <text id="text-15" x="7" y="12" fill="black"
                                                style="font-size: 4pt;font-weight:bold"></text>
                                        </g>
                                    </svg>

                                </div>
                                <input type="text" value="0" id="gigi-15T" name="gigi-15T" hidden>
                                <input type="text" value="0" id="gigi-15B" name="gigi-15B" hidden>
                                <input type="text" value="0" id="gigi-15R" name="gigi-15R" hidden>
                                <input type="text" value="0" id="gigi-15L" name="gigi-15L" hidden>
                                <input type="text" value="0" id="gigi-15C" name="gigi-15C" hidden>

                            </div>
                        </div>
                    </td>
                    <td title="Gigi 14">
                        <div>
                            <h6 class="text-center">14</h6>
                        </div>
                        <div>
                            <div>
                                <div class="custom-button mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <g>
                                            <polygon points="0,0 20,0 15,5 5,5" fill="white" class="gigi hover"
                                                accesskey="14T" stroke="navy" stroke-width="0.5" id="v-14T"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,15 15,15 20,20 0,20" fill="white" class="gigi hover"
                                                accesskey="14B" stroke="navy" stroke-width="0.5" id="v-14B"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="15,5 20,0 20,20 15,15" fill="white" class="gigi hover"
                                                accesskey="14R" stroke="navy" stroke-width="0.5" id="v-14R"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="0,0 5,5 5,15 0,20" fill="white" class="gigi hover"
                                                accesskey="14L" stroke="navy" stroke-width="0.5" id="v-14L"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,5 15,5 15,15 5,15" fill="white" class="gigi hover"
                                                accesskey="14C" stroke="navy" stroke-width="0.5" id="v-14C"
                                                opacity="1">
                                            </polygon>
                                            <text id="text-14" x="7" y="12" fill="black"
                                                style="font-size: 4pt;font-weight:bold"></text>
                                        </g>
                                    </svg>

                                </div>
                                <input type="text" value="0" id="gigi-14T" name="gigi-14T" hidden>
                                <input type="text" value="0" id="gigi-14B" name="gigi-14B" hidden>
                                <input type="text" value="0" id="gigi-14R" name="gigi-14R" hidden>
                                <input type="text" value="0" id="gigi-14L" name="gigi-14L" hidden>
                                <input type="text" value="0" id="gigi-14C" name="gigi-14C" hidden>

                            </div>
                        </div>
                    </td>
                    <td title="Gigi 13">
                        <div>
                            <h6 class="text-center">13</h6>
                        </div>
                        <div>
                            <div>
                                <div class="custom-button mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <g>
                                            <polygon points="0,0 20,0 15,5 5,5" fill="white" class="gigi hover"
                                                accesskey="13T" stroke="navy" stroke-width="0.5" id="v-13T"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,15 15,15 20,20 0,20" fill="white" class="gigi hover"
                                                accesskey="13B" stroke="navy" stroke-width="0.5" id="v-13B"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="15,5 20,0 20,20 15,15" fill="white" class="gigi hover"
                                                accesskey="13R" stroke="navy" stroke-width="0.5" id="v-13R"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="0,0 5,5 5,15 0,20" fill="white" class="gigi hover"
                                                accesskey="13L" stroke="navy" stroke-width="0.5" id="v-13L"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,5 15,5 15,15 5,15" fill="white" class="gigi hover"
                                                accesskey="13C" stroke="navy" stroke-width="0.5" id="v-13C"
                                                opacity="1">
                                            </polygon>
                                            <text id="text-13" x="7" y="12" fill="black"
                                                style="font-size: 4pt;font-weight:bold"></text>
                                        </g>
                                    </svg>

                                </div>
                                <input type="text" value="0" id="gigi-13T" name="gigi-13T" hidden>
                                <input type="text" value="0" id="gigi-13B" name="gigi-13B" hidden>
                                <input type="text" value="0" id="gigi-13R" name="gigi-13R" hidden>
                                <input type="text" value="0" id="gigi-13L" name="gigi-13L" hidden>
                                <input type="text" value="0" id="gigi-13C" name="gigi-13C" hidden>

                            </div>
                        </div>
                    </td>
                    <td title="Gigi 12">
                        <div>
                            <h6 class="text-center">12</h6>
                        </div>
                        <div>
                            <div>
                                <div class="custom-button mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <g>
                                            <polygon points="0,0 20,0 15,5 5,5" fill="white" class="gigi hover"
                                                accesskey="12T" stroke="navy" stroke-width="0.5" id="v-12T"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,15 15,15 20,20 0,20" fill="white" class="gigi hover"
                                                accesskey="12B" stroke="navy" stroke-width="0.5" id="v-12B"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="15,5 20,0 20,20 15,15" fill="white" class="gigi hover"
                                                accesskey="12R" stroke="navy" stroke-width="0.5" id="v-12R"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="0,0 5,5 5,15 0,20" fill="white" class="gigi hover"
                                                accesskey="12L" stroke="navy" stroke-width="0.5" id="v-12L"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,5 15,5 15,15 5,15" fill="white" class="gigi hover"
                                                accesskey="12C" stroke="navy" stroke-width="0.5" id="v-12C"
                                                opacity="1">
                                            </polygon>
                                            <text id="text-12" x="7" y="12" fill="black"
                                                style="font-size: 4pt;font-weight:bold"></text>
                                        </g>
                                    </svg>

                                </div>
                                <input type="text" value="0" id="gigi-12T" name="gigi-12T" hidden>
                                <input type="text" value="0" id="gigi-12B" name="gigi-12B" hidden>
                                <input type="text" value="0" id="gigi-12R" name="gigi-12R" hidden>
                                <input type="text" value="0" id="gigi-12L" name="gigi-12L" hidden>
                                <input type="text" value="0" id="gigi-12C" name="gigi-12C" hidden>

                            </div>
                        </div>
                    </td>
                    <td title="Gigi 11">
                        <div>
                            <h6 class="text-center">11</h6>
                        </div>
                        <div>
                            <div>
                                <div class="custom-button mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <g>
                                            <polygon points="0,0 20,0 15,5 5,5" fill="white" class="gigi hover"
                                                accesskey="11T" stroke="navy" stroke-width="0.5" id="v-11T"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,15 15,15 20,20 0,20" fill="white" class="gigi hover"
                                                accesskey="11B" stroke="navy" stroke-width="0.5" id="v-11B"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="15,5 20,0 20,20 15,15" fill="white" class="gigi hover"
                                                accesskey="11R" stroke="navy" stroke-width="0.5" id="v-11R"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="0,0 5,5 5,15 0,20" fill="white" class="gigi hover"
                                                accesskey="11L" stroke="navy" stroke-width="0.5" id="v-11L"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,5 15,5 15,15 5,15" fill="white" class="gigi hover"
                                                accesskey="11C" stroke="navy" stroke-width="0.5" id="v-11C"
                                                opacity="1">
                                            </polygon>
                                            <text id="text-11" x="7" y="12" fill="black"
                                                style="font-size: 4pt;font-weight:bold"></text>
                                        </g>
                                    </svg>

                                </div>
                                <input type="text" value="0" id="gigi-11T" name="gigi-11T" hidden>
                                <input type="text" value="0" id="gigi-11B" name="gigi-11B" hidden>
                                <input type="text" value="0" id="gigi-11R" name="gigi-11R" hidden>
                                <input type="text" value="0" id="gigi-11L" name="gigi-11L" hidden>
                                <input type="text" value="0" id="gigi-11C" name="gigi-11C" hidden>

                            </div>
                        </div>
                    </td>
                    <td class="y"></td>
                    <td title="Gigi 21">
                        <div>
                            <h6 class="text-center">21</h6>
                        </div>
                        <div>
                            <div>
                                <div class="custom-button mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <g>
                                            <polygon points="0,0 20,0 15,5 5,5" fill="white" class="gigi hover"
                                                accesskey="21T" stroke="navy" stroke-width="0.5" id="v-21T"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,15 15,15 20,20 0,20" fill="white" class="gigi hover"
                                                accesskey="21B" stroke="navy" stroke-width="0.5" id="v-21B"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="15,5 20,0 20,20 15,15" fill="white" class="gigi hover"
                                                accesskey="21R" stroke="navy" stroke-width="0.5" id="v-21R"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="0,0 5,5 5,15 0,20" fill="white" class="gigi hover"
                                                accesskey="21L" stroke="navy" stroke-width="0.5" id="v-21L"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,5 15,5 15,15 5,15" fill="white" class="gigi hover"
                                                accesskey="21C" stroke="navy" stroke-width="0.5" id="v-21C"
                                                opacity="1">
                                            </polygon>
                                            <text id="text-21" x="7" y="12" fill="black"
                                                style="font-size: 4pt;font-weight:bold"></text>
                                        </g>
                                    </svg>

                                </div>
                                <input type="text" value="0" id="gigi-21T" name="gigi-21T" hidden>
                                <input type="text" value="0" id="gigi-21B" name="gigi-21B" hidden>
                                <input type="text" value="0" id="gigi-21R" name="gigi-21R" hidden>
                                <input type="text" value="0" id="gigi-21L" name="gigi-21L" hidden>
                                <input type="text" value="0" id="gigi-21C" name="gigi-21C" hidden>

                            </div>
                        </div>
                    </td>
                    <td title="Gigi 22">
                        <div>
                            <h6 class="text-center">22</h6>
                        </div>
                        <div>
                            <div>
                                <div class="custom-button mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <g>
                                            <polygon points="0,0 20,0 15,5 5,5" fill="white" class="gigi hover"
                                                accesskey="22T" stroke="navy" stroke-width="0.5" id="v-22T"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,15 15,15 20,20 0,20" fill="white" class="gigi hover"
                                                accesskey="22B" stroke="navy" stroke-width="0.5" id="v-22B"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="15,5 20,0 20,20 15,15" fill="white" class="gigi hover"
                                                accesskey="22R" stroke="navy" stroke-width="0.5" id="v-22R"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="0,0 5,5 5,15 0,20" fill="white" class="gigi hover"
                                                accesskey="22L" stroke="navy" stroke-width="0.5" id="v-22L"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,5 15,5 15,15 5,15" fill="white" class="gigi hover"
                                                accesskey="22C" stroke="navy" stroke-width="0.5" id="v-22C"
                                                opacity="1">
                                            </polygon>
                                            <text id="text-22" x="7" y="12" fill="black"
                                                style="font-size: 4pt;font-weight:bold"></text>
                                        </g>
                                    </svg>

                                </div>
                                <input type="text" value="0" id="gigi-22T" name="gigi-22T" hidden>
                                <input type="text" value="0" id="gigi-22B" name="gigi-22B" hidden>
                                <input type="text" value="0" id="gigi-22R" name="gigi-22R" hidden>
                                <input type="text" value="0" id="gigi-22L" name="gigi-22L" hidden>
                                <input type="text" value="0" id="gigi-22C" name="gigi-22C" hidden>

                            </div>
                        </div>
                    </td>
                    <td title="Gigi 23">
                        <div>
                            <h6 class="text-center">23</h6>
                        </div>
                        <div>
                            <div>
                                <div class="custom-button mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <g>
                                            <polygon points="0,0 20,0 15,5 5,5" fill="white" class="gigi hover"
                                                accesskey="23T" stroke="navy" stroke-width="0.5" id="v-23T"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,15 15,15 20,20 0,20" fill="white" class="gigi hover"
                                                accesskey="23B" stroke="navy" stroke-width="0.5" id="v-23B"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="15,5 20,0 20,20 15,15" fill="white" class="gigi hover"
                                                accesskey="23R" stroke="navy" stroke-width="0.5" id="v-23R"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="0,0 5,5 5,15 0,20" fill="white" class="gigi hover"
                                                accesskey="23L" stroke="navy" stroke-width="0.5" id="v-23L"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,5 15,5 15,15 5,15" fill="white" class="gigi hover"
                                                accesskey="23C" stroke="navy" stroke-width="0.5" id="v-23C"
                                                opacity="1">
                                            </polygon>
                                            <text id="text-23" x="7" y="12" fill="black"
                                                style="font-size: 4pt;font-weight:bold"></text>
                                        </g>
                                    </svg>

                                </div>
                                <input type="text" value="0" id="gigi-23T" name="gigi-23T" hidden>
                                <input type="text" value="0" id="gigi-23B" name="gigi-23B" hidden>
                                <input type="text" value="0" id="gigi-23R" name="gigi-23R" hidden>
                                <input type="text" value="0" id="gigi-23L" name="gigi-23L" hidden>
                                <input type="text" value="0" id="gigi-23C" name="gigi-23C" hidden>

                            </div>
                        </div>
                    </td>
                    <td title="Gigi 24">
                        <div>
                            <h6 class="text-center">24</h6>
                        </div>
                        <div>
                            <div>
                                <div class="custom-button mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <g>
                                            <polygon points="0,0 20,0 15,5 5,5" fill="white" class="gigi hover"
                                                accesskey="24T" stroke="navy" stroke-width="0.5" id="v-24T"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,15 15,15 20,20 0,20" fill="white" class="gigi hover"
                                                accesskey="24B" stroke="navy" stroke-width="0.5" id="v-24B"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="15,5 20,0 20,20 15,15" fill="white" class="gigi hover"
                                                accesskey="24R" stroke="navy" stroke-width="0.5" id="v-24R"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="0,0 5,5 5,15 0,20" fill="white" class="gigi hover"
                                                accesskey="24L" stroke="navy" stroke-width="0.5" id="v-24L"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,5 15,5 15,15 5,15" fill="white" class="gigi hover"
                                                accesskey="24C" stroke="navy" stroke-width="0.5" id="v-24C"
                                                opacity="1">
                                            </polygon>
                                            <text id="text-24" x="7" y="12" fill="black"
                                                style="font-size: 4pt;font-weight:bold"></text>
                                        </g>
                                    </svg>

                                </div>
                                <input type="text" value="0" id="gigi-24T" name="gigi-24T" hidden>
                                <input type="text" value="0" id="gigi-24B" name="gigi-24B" hidden>
                                <input type="text" value="0" id="gigi-24R" name="gigi-24R" hidden>
                                <input type="text" value="0" id="gigi-24L" name="gigi-24L" hidden>
                                <input type="text" value="0" id="gigi-24C" name="gigi-24C" hidden>

                            </div>
                        </div>
                    </td>
                    <td title="Gigi 25">
                        <div>
                            <h6 class="text-center">25</h6>
                        </div>
                        <div>
                            <div>
                                <div class="custom-button mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <g>
                                            <polygon points="0,0 20,0 15,5 5,5" fill="white" class="gigi hover"
                                                accesskey="25T" stroke="navy" stroke-width="0.5" id="v-25T"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,15 15,15 20,20 0,20" fill="white" class="gigi hover"
                                                accesskey="25B" stroke="navy" stroke-width="0.5" id="v-25B"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="15,5 20,0 20,20 15,15" fill="white" class="gigi hover"
                                                accesskey="25R" stroke="navy" stroke-width="0.5" id="v-25R"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="0,0 5,5 5,15 0,20" fill="white" class="gigi hover"
                                                accesskey="25L" stroke="navy" stroke-width="0.5" id="v-25L"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,5 15,5 15,15 5,15" fill="white" class="gigi hover"
                                                accesskey="25C" stroke="navy" stroke-width="0.5" id="v-25C"
                                                opacity="1">
                                            </polygon>
                                            <text id="text-25" x="7" y="12" fill="black"
                                                style="font-size: 4pt;font-weight:bold"></text>
                                        </g>
                                    </svg>

                                </div>
                                <input type="text" value="0" id="gigi-25T" name="gigi-25T" hidden>
                                <input type="text" value="0" id="gigi-25B" name="gigi-25B" hidden>
                                <input type="text" value="0" id="gigi-25R" name="gigi-25R" hidden>
                                <input type="text" value="0" id="gigi-25L" name="gigi-25L" hidden>
                                <input type="text" value="0" id="gigi-25C" name="gigi-25C" hidden>

                            </div>
                        </div>
                    </td>
                    <td title="Gigi 26">
                        <div>
                            <h6 class="text-center">26</h6>
                        </div>
                        <div>
                            <div>
                                <div class="custom-button mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <g>
                                            <polygon points="0,0 20,0 15,5 5,5" fill="white" class="gigi hover"
                                                accesskey="26T" stroke="navy" stroke-width="0.5" id="v-26T"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,15 15,15 20,20 0,20" fill="white" class="gigi hover"
                                                accesskey="26B" stroke="navy" stroke-width="0.5" id="v-26B"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="15,5 20,0 20,20 15,15" fill="white" class="gigi hover"
                                                accesskey="26R" stroke="navy" stroke-width="0.5" id="v-26R"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="0,0 5,5 5,15 0,20" fill="white" class="gigi hover"
                                                accesskey="26L" stroke="navy" stroke-width="0.5" id="v-26L"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,5 15,5 15,15 5,15" fill="white" class="gigi hover"
                                                accesskey="26C" stroke="navy" stroke-width="0.5" id="v-26C"
                                                opacity="1">
                                            </polygon>
                                            <text id="text-26" x="7" y="12" fill="black"
                                                style="font-size: 4pt;font-weight:bold"></text>
                                        </g>
                                    </svg>

                                </div>
                                <input type="text" value="0" id="gigi-26T" name="gigi-26T" hidden>
                                <input type="text" value="0" id="gigi-26B" name="gigi-26B" hidden>
                                <input type="text" value="0" id="gigi-26R" name="gigi-26R" hidden>
                                <input type="text" value="0" id="gigi-26L" name="gigi-26L" hidden>
                                <input type="text" value="0" id="gigi-26C" name="gigi-26C" hidden>
                            </div>
                        </div>
                    </td>
                    <td title="Gigi 27">
                        <div>
                            <h6 class="text-center">27</h6>
                        </div>
                        <div>
                            <div>
                                <div class="custom-button mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <g>
                                            <polygon points="0,0 20,0 15,5 5,5" fill="white" class="gigi hover"
                                                accesskey="27T" stroke="navy" stroke-width="0.5" id="v-27T"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,15 15,15 20,20 0,20" fill="white" class="gigi hover"
                                                accesskey="27B" stroke="navy" stroke-width="0.5" id="v-27B"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="15,5 20,0 20,20 15,15" fill="white" class="gigi hover"
                                                accesskey="27R" stroke="navy" stroke-width="0.5" id="v-27R"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="0,0 5,5 5,15 0,20" fill="white" class="gigi hover"
                                                accesskey="27L" stroke="navy" stroke-width="0.5" id="v-27L"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,5 15,5 15,15 5,15" fill="white"
                                                class="gigi hover" accesskey="27C" stroke="navy"
                                                stroke-width="0.5" id="v-27C" opacity="1">
                                            </polygon>
                                            <text id="text-27" x="7" y="12" fill="black"
                                                style="font-size: 4pt;font-weight:bold"></text>
                                        </g>
                                    </svg>

                                </div>
                                <input type="text" value="0" id="gigi-27T" name="gigi-27T" hidden>
                                <input type="text" value="0" id="gigi-27B" name="gigi-27B" hidden>
                                <input type="text" value="0" id="gigi-27R" name="gigi-27R" hidden>
                                <input type="text" value="0" id="gigi-27L" name="gigi-27L" hidden>
                                <input type="text" value="0" id="gigi-27C" name="gigi-27C" hidden>
                            </div>
                        </div>
                    </td>
                    <td title="Gigi 28">
                        <div>
                            <h6 class="text-center">28</h6>
                        </div>
                        <div>
                            <div>
                                <div class="custom-button mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <g>
                                            <polygon points="0,0 20,0 15,5 5,5" fill="white" class="gigi hover"
                                                accesskey="28T" stroke="navy" stroke-width="0.5" id="v-28T"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,15 15,15 20,20 0,20" fill="white"
                                                class="gigi hover" accesskey="28B" stroke="navy"
                                                stroke-width="0.5" id="v-28B" opacity="1">
                                            </polygon>
                                            <polygon points="15,5 20,0 20,20 15,15" fill="white"
                                                class="gigi hover" accesskey="28R" stroke="navy"
                                                stroke-width="0.5" id="v-28R" opacity="1">
                                            </polygon>
                                            <polygon points="0,0 5,5 5,15 0,20" fill="white" class="gigi hover"
                                                accesskey="28L" stroke="navy" stroke-width="0.5" id="v-28L"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,5 15,5 15,15 5,15" fill="white"
                                                class="gigi hover" accesskey="28C" stroke="navy"
                                                stroke-width="0.5" id="v-28C" opacity="1">
                                            </polygon>
                                            <text id="text-28" x="7" y="12" fill="black"
                                                style="font-size: 4pt;font-weight:bold"></text>
                                        </g>
                                    </svg>

                                </div>
                                <input type="text" value="0" id="gigi-28T" name="gigi-28T" hidden>
                                <input type="text" value="0" id="gigi-28B" name="gigi-28B" hidden>
                                <input type="text" value="0" id="gigi-28R" name="gigi-28R" hidden>
                                <input type="text" value="0" id="gigi-28L" name="gigi-28L" hidden>
                                <input type="text" value="0" id="gigi-28C" name="gigi-28C" hidden>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td title="Gigi 55">
                        <div>
                            <h6 class="text-center">55</h6>
                        </div>
                        <div>
                            <div>
                                <div class="custom-button mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <g>
                                            <polygon points="0,0 20,0 15,5 5,5" fill="white" class="gigi hover"
                                                accesskey="55T" stroke="navy" stroke-width="0.5" id="v-55T"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,15 15,15 20,20 0,20" fill="white"
                                                class="gigi hover" accesskey="55B" stroke="navy"
                                                stroke-width="0.5" id="v-55B" opacity="1">
                                            </polygon>
                                            <polygon points="15,5 20,0 20,20 15,15" fill="white"
                                                class="gigi hover" accesskey="55R" stroke="navy"
                                                stroke-width="0.5" id="v-55R" opacity="1">
                                            </polygon>
                                            <polygon points="0,0 5,5 5,15 0,20" fill="white" class="gigi hover"
                                                accesskey="55L" stroke="navy" stroke-width="0.5" id="v-55L"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,5 15,5 15,15 5,15" fill="white"
                                                class="gigi hover" accesskey="55C" stroke="navy"
                                                stroke-width="0.5" id="v-55C" opacity="1">
                                            </polygon>
                                            <text id="text-55" x="7" y="12" fill="black"
                                                style="font-size: 4pt;font-weight:bold"></text>
                                        </g>
                                    </svg>

                                </div>
                                <input type="text" value="0" id="gigi-55T" name="gigi-55T" hidden>
                                <input type="text" value="0" id="gigi-55B" name="gigi-55B" hidden>
                                <input type="text" value="0" id="gigi-55R" name="gigi-55R" hidden>
                                <input type="text" value="0" id="gigi-55L" name="gigi-55L" hidden>
                                <input type="text" value="0" id="gigi-55C" name="gigi-55C" hidden>
                            </div>
                        </div>
                    </td>
                    <td title="Gigi 54">
                        <div>
                            <h6 class="text-center">54</h6>
                        </div>
                        <div>
                            <div>
                                <div class="custom-button mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <g>
                                            <polygon points="0,0 20,0 15,5 5,5" fill="white" class="gigi hover"
                                                accesskey="54T" stroke="navy" stroke-width="0.5" id="v-54T"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,15 15,15 20,20 0,20" fill="white"
                                                class="gigi hover" accesskey="54B" stroke="navy"
                                                stroke-width="0.5" id="v-54B" opacity="1">
                                            </polygon>
                                            <polygon points="15,5 20,0 20,20 15,15" fill="white"
                                                class="gigi hover" accesskey="54R" stroke="navy"
                                                stroke-width="0.5" id="v-54R" opacity="1">
                                            </polygon>
                                            <polygon points="0,0 5,5 5,15 0,20" fill="white" class="gigi hover"
                                                accesskey="54L" stroke="navy" stroke-width="0.5" id="v-54L"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,5 15,5 15,15 5,15" fill="white"
                                                class="gigi hover" accesskey="54C" stroke="navy"
                                                stroke-width="0.5" id="v-54C" opacity="1">
                                            </polygon>
                                            <text id="text-54" x="7" y="12" fill="black"
                                                style="font-size: 4pt;font-weight:bold"></text>
                                        </g>
                                    </svg>

                                </div>
                                <input type="text" value="0" id="gigi-54T" name="gigi-54T" hidden>
                                <input type="text" value="0" id="gigi-54B" name="gigi-54B" hidden>
                                <input type="text" value="0" id="gigi-54R" name="gigi-54R" hidden>
                                <input type="text" value="0" id="gigi-54L" name="gigi-54L" hidden>
                                <input type="text" value="0" id="gigi-54C" name="gigi-54C" hidden>
                            </div>
                        </div>
                    </td>
                    <td title="Gigi 53">
                        <div>
                            <h6 class="text-center">53</h6>
                        </div>
                        <div>
                            <div>
                                <div class="custom-button mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <g>
                                            <polygon points="0,0 20,0 15,5 5,5" fill="white" class="gigi hover"
                                                accesskey="53T" stroke="navy" stroke-width="0.5" id="v-53T"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,15 15,15 20,20 0,20" fill="white"
                                                class="gigi hover" accesskey="53B" stroke="navy"
                                                stroke-width="0.5" id="v-53B" opacity="1">
                                            </polygon>
                                            <polygon points="15,5 20,0 20,20 15,15" fill="white"
                                                class="gigi hover" accesskey="53R" stroke="navy"
                                                stroke-width="0.5" id="v-53R" opacity="1">
                                            </polygon>
                                            <polygon points="0,0 5,5 5,15 0,20" fill="white" class="gigi hover"
                                                accesskey="53L" stroke="navy" stroke-width="0.5" id="v-53L"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,5 15,5 15,15 5,15" fill="white"
                                                class="gigi hover" accesskey="53C" stroke="navy"
                                                stroke-width="0.5" id="v-53C" opacity="1">
                                            </polygon>
                                            <text id="text-53" x="7" y="12" fill="black"
                                                style="font-size: 4pt;font-weight:bold"></text>
                                        </g>
                                    </svg>

                                </div>
                                <input type="text" value="0" id="gigi-53T" name="gigi-53T" hidden>
                                <input type="text" value="0" id="gigi-53B" name="gigi-53B" hidden>
                                <input type="text" value="0" id="gigi-53R" name="gigi-53R" hidden>
                                <input type="text" value="0" id="gigi-53L" name="gigi-53L" hidden>
                                <input type="text" value="0" id="gigi-53C" name="gigi-53C" hidden>
                            </div>
                        </div>
                    </td>
                    <td title="Gigi 52">
                        <div>
                            <h6 class="text-center">52</h6>
                        </div>
                        <div>
                            <div>
                                <div class="custom-button mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <g>
                                            <polygon points="0,0 20,0 15,5 5,5" fill="white" class="gigi hover"
                                                accesskey="52T" stroke="navy" stroke-width="0.5" id="v-52T"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,15 15,15 20,20 0,20" fill="white"
                                                class="gigi hover" accesskey="52B" stroke="navy"
                                                stroke-width="0.5" id="v-52B" opacity="1">
                                            </polygon>
                                            <polygon points="15,5 20,0 20,20 15,15" fill="white"
                                                class="gigi hover" accesskey="52R" stroke="navy"
                                                stroke-width="0.5" id="v-52R" opacity="1">
                                            </polygon>
                                            <polygon points="0,0 5,5 5,15 0,20" fill="white" class="gigi hover"
                                                accesskey="52L" stroke="navy" stroke-width="0.5" id="v-52L"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,5 15,5 15,15 5,15" fill="white"
                                                class="gigi hover" accesskey="52C" stroke="navy"
                                                stroke-width="0.5" id="v-52C" opacity="1">
                                            </polygon>
                                            <text id="text-52" x="7" y="12" fill="black"
                                                style="font-size: 4pt;font-weight:bold"></text>
                                        </g>
                                    </svg>

                                </div>
                                <input type="text" value="0" id="gigi-52T" name="gigi-52T" hidden>
                                <input type="text" value="0" id="gigi-52B" name="gigi-52B" hidden>
                                <input type="text" value="0" id="gigi-52R" name="gigi-52R" hidden>
                                <input type="text" value="0" id="gigi-52L" name="gigi-52L" hidden>
                                <input type="text" value="0" id="gigi-52C" name="gigi-52C" hidden>
                            </div>
                        </div>
                    </td>
                    <td title="Gigi 51">
                        <div>
                            <h6 class="text-center">51</h6>
                        </div>
                        <div>
                            <div>
                                <div class="custom-button mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <g>
                                            <polygon points="0,0 20,0 15,5 5,5" fill="white" class="gigi hover"
                                                accesskey="51T" stroke="navy" stroke-width="0.5" id="v-51T"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,15 15,15 20,20 0,20" fill="white"
                                                class="gigi hover" accesskey="51B" stroke="navy"
                                                stroke-width="0.5" id="v-51B" opacity="1">
                                            </polygon>
                                            <polygon points="15,5 20,0 20,20 15,15" fill="white"
                                                class="gigi hover" accesskey="51R" stroke="navy"
                                                stroke-width="0.5" id="v-51R" opacity="1">
                                            </polygon>
                                            <polygon points="0,0 5,5 5,15 0,20" fill="white" class="gigi hover"
                                                accesskey="51L" stroke="navy" stroke-width="0.5" id="v-51L"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,5 15,5 15,15 5,15" fill="white"
                                                class="gigi hover" accesskey="51C" stroke="navy"
                                                stroke-width="0.5" id="v-51C" opacity="1">
                                            </polygon>
                                            <text id="text-51" x="7" y="12" fill="black"
                                                style="font-size: 4pt;font-weight:bold"></text>
                                        </g>
                                    </svg>

                                </div>
                                <input type="text" value="0" id="gigi-51T" name="gigi-51T" hidden>
                                <input type="text" value="0" id="gigi-51B" name="gigi-51B" hidden>
                                <input type="text" value="0" id="gigi-51R" name="gigi-51R" hidden>
                                <input type="text" value="0" id="gigi-51L" name="gigi-51L" hidden>
                                <input type="text" value="0" id="gigi-51C" name="gigi-51C" hidden>
                            </div>
                        </div>
                    </td>
                    <td></td>
                    <td class="y"></td>
                    <td></td>
                    <td title="Gigi 61">
                        <div>
                            <h6 class="text-center">61</h6>
                        </div>
                        <div>
                            <div>
                                <div class="custom-button mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <g>
                                            <polygon points="0,0 20,0 15,5 5,5" fill="white" class="gigi hover"
                                                accesskey="61T" stroke="navy" stroke-width="0.5" id="v-61T"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,15 15,15 20,20 0,20" fill="white"
                                                class="gigi hover" accesskey="61B" stroke="navy"
                                                stroke-width="0.5" id="v-61B" opacity="1">
                                            </polygon>
                                            <polygon points="15,5 20,0 20,20 15,15" fill="white"
                                                class="gigi hover" accesskey="61R" stroke="navy"
                                                stroke-width="0.5" id="v-61R" opacity="1">
                                            </polygon>
                                            <polygon points="0,0 5,5 5,15 0,20" fill="white" class="gigi hover"
                                                accesskey="61L" stroke="navy" stroke-width="0.5" id="v-61L"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,5 15,5 15,15 5,15" fill="white"
                                                class="gigi hover" accesskey="61C" stroke="navy"
                                                stroke-width="0.5" id="v-61C" opacity="1">
                                            </polygon>
                                            <text id="text-61" x="7" y="12" fill="black"
                                                style="font-size: 4pt;font-weight:bold"></text>
                                        </g>
                                    </svg>

                                </div>
                                <input type="text" value="0" id="gigi-61T" name="gigi-61T" hidden>
                                <input type="text" value="0" id="gigi-61B" name="gigi-61B" hidden>
                                <input type="text" value="0" id="gigi-61R" name="gigi-61R" hidden>
                                <input type="text" value="0" id="gigi-61L" name="gigi-61L" hidden>
                                <input type="text" value="0" id="gigi-61C" name="gigi-61C" hidden>
                            </div>
                        </div>
                    </td>
                    <td title="Gigi 62">
                        <div>
                            <h6 class="text-center">62</h6>
                        </div>
                        <div>
                            <div>
                                <div class="custom-button mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <g>
                                            <polygon points="0,0 20,0 15,5 5,5" fill="white" class="gigi hover"
                                                accesskey="62T" stroke="navy" stroke-width="0.5" id="v-62T"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,15 15,15 20,20 0,20" fill="white"
                                                class="gigi hover" accesskey="62B" stroke="navy"
                                                stroke-width="0.5" id="v-62B" opacity="1">
                                            </polygon>
                                            <polygon points="15,5 20,0 20,20 15,15" fill="white"
                                                class="gigi hover" accesskey="62R" stroke="navy"
                                                stroke-width="0.5" id="v-62R" opacity="1">
                                            </polygon>
                                            <polygon points="0,0 5,5 5,15 0,20" fill="white" class="gigi hover"
                                                accesskey="62L" stroke="navy" stroke-width="0.5" id="v-62L"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,5 15,5 15,15 5,15" fill="white"
                                                class="gigi hover" accesskey="62C" stroke="navy"
                                                stroke-width="0.5" id="v-62C" opacity="1">
                                            </polygon>
                                            <text id="text-62" x="7" y="12" fill="black"
                                                style="font-size: 4pt;font-weight:bold"></text>
                                        </g>
                                    </svg>

                                </div>
                                <input type="text" value="0" id="gigi-62T" name="gigi-62T" hidden>
                                <input type="text" value="0" id="gigi-62B" name="gigi-62B" hidden>
                                <input type="text" value="0" id="gigi-62R" name="gigi-62R" hidden>
                                <input type="text" value="0" id="gigi-62L" name="gigi-62L" hidden>
                                <input type="text" value="0" id="gigi-62C" name="gigi-62C" hidden>
                            </div>
                        </div>
                    </td>
                    <td title="Gigi 63">
                        <div>
                            <h6 class="text-center">63</h6>
                        </div>
                        <div>
                            <div>
                                <div class="custom-button mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <g>
                                            <polygon points="0,0 20,0 15,5 5,5" fill="white" class="gigi hover"
                                                accesskey="63T" stroke="navy" stroke-width="0.5" id="v-63T"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,15 15,15 20,20 0,20" fill="white"
                                                class="gigi hover" accesskey="63B" stroke="navy"
                                                stroke-width="0.5" id="v-63B" opacity="1">
                                            </polygon>
                                            <polygon points="15,5 20,0 20,20 15,15" fill="white"
                                                class="gigi hover" accesskey="63R" stroke="navy"
                                                stroke-width="0.5" id="v-63R" opacity="1">
                                            </polygon>
                                            <polygon points="0,0 5,5 5,15 0,20" fill="white" class="gigi hover"
                                                accesskey="63L" stroke="navy" stroke-width="0.5" id="v-63L"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,5 15,5 15,15 5,15" fill="white"
                                                class="gigi hover" accesskey="63C" stroke="navy"
                                                stroke-width="0.5" id="v-63C" opacity="1">
                                            </polygon>
                                            <text id="text-63" x="7" y="12" fill="black"
                                                style="font-size: 4pt;font-weight:bold"></text>
                                        </g>
                                    </svg>

                                </div>
                                <input type="text" value="0" id="gigi-63T" name="gigi-63T" hidden>
                                <input type="text" value="0" id="gigi-63B" name="gigi-63B" hidden>
                                <input type="text" value="0" id="gigi-63R" name="gigi-63R" hidden>
                                <input type="text" value="0" id="gigi-63L" name="gigi-63L" hidden>
                                <input type="text" value="0" id="gigi-63C" name="gigi-63C" hidden>
                            </div>
                        </div>
                    </td>
                    <td title="Gigi 64">
                        <div>
                            <h6 class="text-center">64</h6>
                        </div>
                        <div>
                            <div>
                                <div class="custom-button mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <g>
                                            <polygon points="0,0 20,0 15,5 5,5" fill="white" class="gigi hover"
                                                accesskey="64T" stroke="navy" stroke-width="0.5" id="v-64T"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,15 15,15 20,20 0,20" fill="white"
                                                class="gigi hover" accesskey="64B" stroke="navy"
                                                stroke-width="0.5" id="v-64B" opacity="1">
                                            </polygon>
                                            <polygon points="15,5 20,0 20,20 15,15" fill="white"
                                                class="gigi hover" accesskey="64R" stroke="navy"
                                                stroke-width="0.5" id="v-64R" opacity="1">
                                            </polygon>
                                            <polygon points="0,0 5,5 5,15 0,20" fill="white" class="gigi hover"
                                                accesskey="64L" stroke="navy" stroke-width="0.5" id="v-64L"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,5 15,5 15,15 5,15" fill="white"
                                                class="gigi hover" accesskey="64C" stroke="navy"
                                                stroke-width="0.5" id="v-64C" opacity="1">
                                            </polygon>
                                            <text id="text-64" x="7" y="12" fill="black"
                                                style="font-size: 4pt;font-weight:bold"></text>
                                        </g>
                                    </svg>

                                </div>
                                <input type="text" value="0" id="gigi-64T" name="gigi-64T" hidden>
                                <input type="text" value="0" id="gigi-64B" name="gigi-64B" hidden>
                                <input type="text" value="0" id="gigi-64R" name="gigi-64R" hidden>
                                <input type="text" value="0" id="gigi-64L" name="gigi-64L" hidden>
                                <input type="text" value="0" id="gigi-64C" name="gigi-64C" hidden>
                            </div>
                        </div>
                    </td>
                    <td title="Gigi 65">
                        <div>
                            <h6 class="text-center">65</h6>
                        </div>
                        <div>
                            <div>
                                <div class="custom-button mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <g>
                                            <polygon points="0,0 20,0 15,5 5,5" fill="white" class="gigi hover"
                                                accesskey="65T" stroke="navy" stroke-width="0.5" id="v-65T"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,15 15,15 20,20 0,20" fill="white"
                                                class="gigi hover" accesskey="65B" stroke="navy"
                                                stroke-width="0.5" id="v-65B" opacity="1">
                                            </polygon>
                                            <polygon points="15,5 20,0 20,20 15,15" fill="white"
                                                class="gigi hover" accesskey="65R" stroke="navy"
                                                stroke-width="0.5" id="v-65R" opacity="1">
                                            </polygon>
                                            <polygon points="0,0 5,5 5,15 0,20" fill="white" class="gigi hover"
                                                accesskey="65L" stroke="navy" stroke-width="0.5" id="v-65L"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,5 15,5 15,15 5,15" fill="white"
                                                class="gigi hover" accesskey="65C" stroke="navy"
                                                stroke-width="0.5" id="v-65C" opacity="1">
                                            </polygon>
                                            <text id="text-65" x="7" y="12" fill="black"
                                                style="font-size: 4pt;font-weight:bold"></text>
                                        </g>
                                    </svg>

                                </div>
                                <input type="text" value="0" id="gigi-65T" name="gigi-65T" hidden>
                                <input type="text" value="0" id="gigi-65B" name="gigi-65B" hidden>
                                <input type="text" value="0" id="gigi-65R" name="gigi-65R" hidden>
                                <input type="text" value="0" id="gigi-65L" name="gigi-65L" hidden>
                                <input type="text" value="0" id="gigi-65C" name="gigi-65C" hidden>
                            </div>
                        </div>
                    </td>
                    <td></td>
                    <td></td>
                </tr>
                <tr class="letras">
                    <td class="x"></td>
                    <td class="x"></td>
                    <td class="x text-center"><strong>E</strong><br></td>
                    <td class="y text-center"><strong>D</strong><br></td>
                    <td class="y text-center"><strong>C</strong><br></td>
                    <td class="y text-center"><strong>B</strong><br></td>
                    <td class="y text-center"><strong>A</strong><br></td>
                    <td class="y"></td>
                    <td class="y"></td>
                    <td class="y"></td>
                    <td class="y text-center"><strong>A</strong></td>
                    <td class="y text-center"><strong>B</strong></td>
                    <td class="y text-center"><strong>C</strong></td>
                    <td class="y text-center"><strong>D</strong></td>
                    <td class="y text-center"><strong>E</strong></td>
                    <td class="y"></td>
                    <td class="y"></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td title="Gigi 85">
                        <div>
                            <div>
                                <div class="custom-button mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <g>
                                            <polygon points="0,0 20,0 15,5 5,5" fill="white" class="gigi hover"
                                                accesskey="85T" stroke="navy" stroke-width="0.5" id="v-85T"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,15 15,15 20,20 0,20" fill="white"
                                                class="gigi hover" accesskey="85B" stroke="navy"
                                                stroke-width="0.5" id="v-85B" opacity="1">
                                            </polygon>
                                            <polygon points="15,5 20,0 20,20 15,15" fill="white"
                                                class="gigi hover" accesskey="85R" stroke="navy"
                                                stroke-width="0.5" id="v-85R" opacity="1">
                                            </polygon>
                                            <polygon points="0,0 5,5 5,15 0,20" fill="white" class="gigi hover"
                                                accesskey="85L" stroke="navy" stroke-width="0.5" id="v-85L"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,5 15,5 15,15 5,15" fill="white"
                                                class="gigi hover" accesskey="85C" stroke="navy"
                                                stroke-width="0.5" id="v-85C" opacity="1">
                                            </polygon>
                                            <text id="text-85" x="7" y="12" fill="black"
                                                style="font-size: 4pt;font-weight:bold"></text>
                                        </g>
                                    </svg>

                                </div>
                                <input type="text" value="0" id="gigi-85T" name="gigi-85T" hidden>
                                <input type="text" value="0" id="gigi-85B" name="gigi-85B" hidden>
                                <input type="text" value="0" id="gigi-85R" name="gigi-85R" hidden>
                                <input type="text" value="0" id="gigi-85L" name="gigi-85L" hidden>
                                <input type="text" value="0" id="gigi-85C" name="gigi-85C" hidden>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-center">85</h6>
                        </div>
                    </td>
                    <td title="Gigi 84">
                        <div>
                            <div>
                                <div class="custom-button mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <g>
                                            <polygon points="0,0 20,0 15,5 5,5" fill="white" class="gigi hover"
                                                accesskey="84T" stroke="navy" stroke-width="0.5" id="v-84T"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,15 15,15 20,20 0,20" fill="white"
                                                class="gigi hover" accesskey="84B" stroke="navy"
                                                stroke-width="0.5" id="v-84B" opacity="1">
                                            </polygon>
                                            <polygon points="15,5 20,0 20,20 15,15" fill="white"
                                                class="gigi hover" accesskey="84R" stroke="navy"
                                                stroke-width="0.5" id="v-84R" opacity="1">
                                            </polygon>
                                            <polygon points="0,0 5,5 5,15 0,20" fill="white" class="gigi hover"
                                                accesskey="84L" stroke="navy" stroke-width="0.5" id="v-84L"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,5 15,5 15,15 5,15" fill="white"
                                                class="gigi hover" accesskey="84C" stroke="navy"
                                                stroke-width="0.5" id="v-84C" opacity="1">
                                            </polygon>
                                            <text id="text-84" x="7" y="12" fill="black"
                                                style="font-size: 4pt;font-weight:bold"></text>
                                        </g>
                                    </svg>

                                </div>
                                <input type="text" value="0" id="gigi-84T" name="gigi-84T" hidden>
                                <input type="text" value="0" id="gigi-84B" name="gigi-84B" hidden>
                                <input type="text" value="0" id="gigi-84R" name="gigi-84R" hidden>
                                <input type="text" value="0" id="gigi-84L" name="gigi-84L" hidden>
                                <input type="text" value="0" id="gigi-84C" name="gigi-84C" hidden>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-center">84</h6>
                        </div>
                    </td>
                    <td title="Gigi 83">
                        <div>
                            <div>
                                <div class="custom-button mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <g>
                                            <polygon points="0,0 20,0 15,5 5,5" fill="white" class="gigi hover"
                                                accesskey="83T" stroke="navy" stroke-width="0.5" id="v-83T"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,15 15,15 20,20 0,20" fill="white"
                                                class="gigi hover" accesskey="83B" stroke="navy"
                                                stroke-width="0.5" id="v-83B" opacity="1">
                                            </polygon>
                                            <polygon points="15,5 20,0 20,20 15,15" fill="white"
                                                class="gigi hover" accesskey="83R" stroke="navy"
                                                stroke-width="0.5" id="v-83R" opacity="1">
                                            </polygon>
                                            <polygon points="0,0 5,5 5,15 0,20" fill="white" class="gigi hover"
                                                accesskey="83L" stroke="navy" stroke-width="0.5" id="v-83L"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,5 15,5 15,15 5,15" fill="white"
                                                class="gigi hover" accesskey="83C" stroke="navy"
                                                stroke-width="0.5" id="v-83C" opacity="1">
                                            </polygon>
                                            <text id="text-83" x="7" y="12" fill="black"
                                                style="font-size: 4pt;font-weight:bold"></text>
                                        </g>
                                    </svg>

                                </div>
                                <input type="text" value="0" id="gigi-83T" name="gigi-83T" hidden>
                                <input type="text" value="0" id="gigi-83B" name="gigi-83B" hidden>
                                <input type="text" value="0" id="gigi-83R" name="gigi-83R" hidden>
                                <input type="text" value="0" id="gigi-83L" name="gigi-83L" hidden>
                                <input type="text" value="0" id="gigi-83C" name="gigi-83C" hidden>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-center">83</h6>
                        </div>
                    </td>
                    <td title="Gigi 82">
                        <div>
                            <div>
                                <div class="custom-button mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <g>
                                            <polygon points="0,0 20,0 15,5 5,5" fill="white" class="gigi hover"
                                                accesskey="82T" stroke="navy" stroke-width="0.5" id="v-82T"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,15 15,15 20,20 0,20" fill="white"
                                                class="gigi hover" accesskey="82B" stroke="navy"
                                                stroke-width="0.5" id="v-82B" opacity="1">
                                            </polygon>
                                            <polygon points="15,5 20,0 20,20 15,15" fill="white"
                                                class="gigi hover" accesskey="82R" stroke="navy"
                                                stroke-width="0.5" id="v-82R" opacity="1">
                                            </polygon>
                                            <polygon points="0,0 5,5 5,15 0,20" fill="white" class="gigi hover"
                                                accesskey="82L" stroke="navy" stroke-width="0.5" id="v-82L"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,5 15,5 15,15 5,15" fill="white"
                                                class="gigi hover" accesskey="82C" stroke="navy"
                                                stroke-width="0.5" id="v-82C" opacity="1">
                                            </polygon>
                                            <text id="text-82" x="7" y="12" fill="black"
                                                style="font-size: 4pt;font-weight:bold"></text>
                                        </g>
                                    </svg>

                                </div>
                                <input type="text" value="0" id="gigi-82T" name="gigi-82T" hidden>
                                <input type="text" value="0" id="gigi-82B" name="gigi-82B" hidden>
                                <input type="text" value="0" id="gigi-82R" name="gigi-82R" hidden>
                                <input type="text" value="0" id="gigi-82L" name="gigi-82L" hidden>
                                <input type="text" value="0" id="gigi-82C" name="gigi-82C" hidden>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-center">82</h6>
                        </div>
                    </td>
                    <td title="Gigi 81">
                        <div>
                            <div>
                                <div class="custom-button mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <g>
                                            <polygon points="0,0 20,0 15,5 5,5" fill="white" class="gigi hover"
                                                accesskey="81T" stroke="navy" stroke-width="0.5" id="v-81T"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,15 15,15 20,20 0,20" fill="white"
                                                class="gigi hover" accesskey="81B" stroke="navy"
                                                stroke-width="0.5" id="v-81B" opacity="1">
                                            </polygon>
                                            <polygon points="15,5 20,0 20,20 15,15" fill="white"
                                                class="gigi hover" accesskey="81R" stroke="navy"
                                                stroke-width="0.5" id="v-81R" opacity="1">
                                            </polygon>
                                            <polygon points="0,0 5,5 5,15 0,20" fill="white" class="gigi hover"
                                                accesskey="81L" stroke="navy" stroke-width="0.5" id="v-81L"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,5 15,5 15,15 5,15" fill="white"
                                                class="gigi hover" accesskey="81C" stroke="navy"
                                                stroke-width="0.5" id="v-81C" opacity="1">
                                            </polygon>
                                            <text id="text-81" x="7" y="12" fill="black"
                                                style="font-size: 4pt;font-weight:bold"></text>
                                        </g>
                                    </svg>

                                </div>
                                <input type="text" value="0" id="gigi-81T" name="gigi-81T" hidden>
                                <input type="text" value="0" id="gigi-81B" name="gigi-81B" hidden>
                                <input type="text" value="0" id="gigi-81R" name="gigi-81R" hidden>
                                <input type="text" value="0" id="gigi-81L" name="gigi-81L" hidden>
                                <input type="text" value="0" id="gigi-81C" name="gigi-81C" hidden>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-center">81</h6>
                        </div>

                    </td>
                    <td></td>
                    <td class="y"></td>
                    <td></td>
                    <td title="Gigi 71">
                        <div>
                            <div>
                                <div class="custom-button mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <g>
                                            <polygon points="0,0 20,0 15,5 5,5" fill="white" class="gigi hover"
                                                accesskey="71T" stroke="navy" stroke-width="0.5" id="v-71T"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,15 15,15 20,20 0,20" fill="white"
                                                class="gigi hover" accesskey="71B" stroke="navy"
                                                stroke-width="0.5" id="v-71B" opacity="1">
                                            </polygon>
                                            <polygon points="15,5 20,0 20,20 15,15" fill="white"
                                                class="gigi hover" accesskey="71R" stroke="navy"
                                                stroke-width="0.5" id="v-71R" opacity="1">
                                            </polygon>
                                            <polygon points="0,0 5,5 5,15 0,20" fill="white" class="gigi hover"
                                                accesskey="71L" stroke="navy" stroke-width="0.5" id="v-71L"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,5 15,5 15,15 5,15" fill="white"
                                                class="gigi hover" accesskey="71C" stroke="navy"
                                                stroke-width="0.5" id="v-71C" opacity="1">
                                            </polygon>
                                            <text id="text-71" x="7" y="12" fill="black"
                                                style="font-size: 4pt;font-weight:bold"></text>
                                        </g>
                                    </svg>

                                </div>
                                <input type="text" value="0" id="gigi-71T" name="gigi-71T" hidden>
                                <input type="text" value="0" id="gigi-71B" name="gigi-71B" hidden>
                                <input type="text" value="0" id="gigi-71R" name="gigi-71R" hidden>
                                <input type="text" value="0" id="gigi-71L" name="gigi-71L" hidden>
                                <input type="text" value="0" id="gigi-71C" name="gigi-71C" hidden>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-center">71</h6>
                        </div>

                    </td>
                    <td title="Gigi 72">
                        <div>
                            <div>
                                <div class="custom-button mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <g>
                                            <polygon points="0,0 20,0 15,5 5,5" fill="white" class="gigi hover"
                                                accesskey="72T" stroke="navy" stroke-width="0.5" id="v-72T"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,15 15,15 20,20 0,20" fill="white"
                                                class="gigi hover" accesskey="72B" stroke="navy"
                                                stroke-width="0.5" id="v-72B" opacity="1">
                                            </polygon>
                                            <polygon points="15,5 20,0 20,20 15,15" fill="white"
                                                class="gigi hover" accesskey="72R" stroke="navy"
                                                stroke-width="0.5" id="v-72R" opacity="1">
                                            </polygon>
                                            <polygon points="0,0 5,5 5,15 0,20" fill="white" class="gigi hover"
                                                accesskey="72L" stroke="navy" stroke-width="0.5" id="v-72L"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,5 15,5 15,15 5,15" fill="white"
                                                class="gigi hover" accesskey="72C" stroke="navy"
                                                stroke-width="0.5" id="v-72C" opacity="1">
                                            </polygon>
                                            <text id="text-72" x="7" y="12" fill="black"
                                                style="font-size: 4pt;font-weight:bold"></text>
                                        </g>
                                    </svg>

                                </div>
                                <input type="text" value="0" id="gigi-72T" name="gigi-72T" hidden>
                                <input type="text" value="0" id="gigi-72B" name="gigi-72B" hidden>
                                <input type="text" value="0" id="gigi-72R" name="gigi-72R" hidden>
                                <input type="text" value="0" id="gigi-72L" name="gigi-72L" hidden>
                                <input type="text" value="0" id="gigi-72C" name="gigi-72C" hidden>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-center">72</h6>
                        </div>

                    </td>
                    <td title="Gigi 73">
                        <div>
                            <div>
                                <div class="custom-button mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <g>
                                            <polygon points="0,0 20,0 15,5 5,5" fill="white" class="gigi hover"
                                                accesskey="73T" stroke="navy" stroke-width="0.5" id="v-73T"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,15 15,15 20,20 0,20" fill="white"
                                                class="gigi hover" accesskey="73B" stroke="navy"
                                                stroke-width="0.5" id="v-73B" opacity="1">
                                            </polygon>
                                            <polygon points="15,5 20,0 20,20 15,15" fill="white"
                                                class="gigi hover" accesskey="73R" stroke="navy"
                                                stroke-width="0.5" id="v-73R" opacity="1">
                                            </polygon>
                                            <polygon points="0,0 5,5 5,15 0,20" fill="white" class="gigi hover"
                                                accesskey="73L" stroke="navy" stroke-width="0.5" id="v-73L"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,5 15,5 15,15 5,15" fill="white"
                                                class="gigi hover" accesskey="73C" stroke="navy"
                                                stroke-width="0.5" id="v-73C" opacity="1">
                                            </polygon>
                                            <text id="text-73" x="7" y="12" fill="black"
                                                style="font-size: 4pt;font-weight:bold"></text>
                                        </g>
                                    </svg>

                                </div>
                                <input type="text" value="0" id="gigi-73T" name="gigi-73T" hidden>
                                <input type="text" value="0" id="gigi-73B" name="gigi-73B" hidden>
                                <input type="text" value="0" id="gigi-73R" name="gigi-73R" hidden>
                                <input type="text" value="0" id="gigi-73L" name="gigi-73L" hidden>
                                <input type="text" value="0" id="gigi-73C" name="gigi-73C" hidden>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-center">73</h6>

                        </div>
                    </td>
                    <td title="Gigi 74">
                        <div>
                            <div>
                                <div>
                                    <div class="custom-button mx-auto">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <g>
                                                <polygon points="0,0 20,0 15,5 5,5" fill="white"
                                                    class="gigi hover" accesskey="74T" stroke="navy"
                                                    stroke-width="0.5" id="v-74T" opacity="1"></polygon>
                                                <polygon points="5,15 15,15 20,20 0,20" fill="white"
                                                    class="gigi hover" accesskey="74B" stroke="navy"
                                                    stroke-width="0.5" id="v-74B" opacity="1"></polygon>
                                                <polygon points="15,5 20,0 20,20 15,15" fill="white"
                                                    class="gigi hover" accesskey="74R" stroke="navy"
                                                    stroke-width="0.5" id="v-74R" opacity="1"></polygon>
                                                <polygon points="0,0 5,5 5,15 0,20" fill="white"
                                                    class="gigi hover" accesskey="74L" stroke="navy"
                                                    stroke-width="0.5" id="v-74L" opacity="1"></polygon>
                                                <polygon points="5,5 15,5 15,15 5,15" fill="white"
                                                    class="gigi hover" accesskey="74C" stroke="navy"
                                                    stroke-width="0.5" id="v-74C" opacity="1"></polygon>
                                                <text id="text-74" x="7" y="12" fill="black"
                                                    style="font-size: 4pt;font-weight:bold"></text>
                                            </g>
                                        </svg>

                                    </div>
                                    <input type="text" value="0" id="gigi-74T" name="gigi-74T" hidden>
                                    <input type="text" value="0" id="gigi-74B" name="gigi-74B" hidden>
                                    <input type="text" value="0" id="gigi-74R" name="gigi-74R" hidden>
                                    <input type="text" value="0" id="gigi-74L" name="gigi-74L" hidden>
                                    <input type="text" value="0" id="gigi-74C" name="gigi-74C" hidden>
                                </div>
                            </div>
                            <div>
                                <h6 class="text-center">74</h6>

                            </div>
                    </td>
                    <td title="Gigi 75">
                        <div>
                            <div>
                                <div class="custom-button mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <g>
                                            <polygon points="0,0 20,0 15,5 5,5" fill="white" class="gigi hover"
                                                accesskey="75T" stroke="navy" stroke-width="0.5" id="v-75T"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,15 15,15 20,20 0,20" fill="white"
                                                class="gigi hover" accesskey="75B" stroke="navy"
                                                stroke-width="0.5" id="v-75B" opacity="1">
                                            </polygon>
                                            <polygon points="15,5 20,0 20,20 15,15" fill="white"
                                                class="gigi hover" accesskey="75R" stroke="navy"
                                                stroke-width="0.5" id="v-75R" opacity="1">
                                            </polygon>
                                            <polygon points="0,0 5,5 5,15 0,20" fill="white" class="gigi hover"
                                                accesskey="75L" stroke="navy" stroke-width="0.5" id="v-75L"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,5 15,5 15,15 5,15" fill="white"
                                                class="gigi hover" accesskey="75C" stroke="navy"
                                                stroke-width="0.5" id="v-75C" opacity="1">
                                            </polygon>
                                            <text id="text-75" x="7" y="12" fill="black"
                                                style="font-size: 4pt;font-weight:bold"></text>
                                        </g>
                                    </svg>

                                </div>
                                <input type="text" value="0" id="gigi-75T" name="gigi-75T" hidden>
                                <input type="text" value="0" id="gigi-75B" name="gigi-75B" hidden>
                                <input type="text" value="0" id="gigi-75R" name="gigi-75R" hidden>
                                <input type="text" value="0" id="gigi-75L" name="gigi-75L" hidden>
                                <input type="text" value="0" id="gigi-75C" name="gigi-75C" hidden>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-center">75</h6>
                        </div>

                    </td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td title="Gigi 48">
                        <div>
                            <div>
                                <div class="custom-button mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <g>
                                            <polygon points="0,0 20,0 15,5 5,5" fill="white" class="gigi hover"
                                                accesskey="48T" stroke="navy" stroke-width="0.5" id="v-48T"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,15 15,15 20,20 0,20" fill="white"
                                                class="gigi hover" accesskey="48B" stroke="navy"
                                                stroke-width="0.5" id="v-48B" opacity="1">
                                            </polygon>
                                            <polygon points="15,5 20,0 20,20 15,15" fill="white"
                                                class="gigi hover" accesskey="48R" stroke="navy"
                                                stroke-width="0.5" id="v-48R" opacity="1">
                                            </polygon>
                                            <polygon points="0,0 5,5 5,15 0,20" fill="white" class="gigi hover"
                                                accesskey="48L" stroke="navy" stroke-width="0.5" id="v-48L"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,5 15,5 15,15 5,15" fill="white"
                                                class="gigi hover" accesskey="48C" stroke="navy"
                                                stroke-width="0.5" id="v-48C" opacity="1">
                                            </polygon>
                                            <text id="text-48" x="7" y="12" fill="black"
                                                style="font-size: 4pt;font-weight:bold"></text>
                                        </g>
                                    </svg>

                                </div>
                                <input type="text" value="0" id="gigi-48T" name="gigi-48T" hidden>
                                <input type="text" value="0" id="gigi-48B" name="gigi-48B" hidden>
                                <input type="text" value="0" id="gigi-48R" name="gigi-48R" hidden>
                                <input type="text" value="0" id="gigi-48L" name="gigi-48L" hidden>
                                <input type="text" value="0" id="gigi-48C" name="gigi-48C" hidden>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-center">48</h6>
                        </div>
                    </td>
                    <td title="Gigi 47">
                        <div>
                            <div>
                                <div class="custom-button mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <g>
                                            <polygon points="0,0 20,0 15,5 5,5" fill="white" class="gigi hover"
                                                accesskey="47T" stroke="navy" stroke-width="0.5" id="v-47T"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,15 15,15 20,20 0,20" fill="white"
                                                class="gigi hover" accesskey="47B" stroke="navy"
                                                stroke-width="0.5" id="v-47B" opacity="1">
                                            </polygon>
                                            <polygon points="15,5 20,0 20,20 15,15" fill="white"
                                                class="gigi hover" accesskey="47R" stroke="navy"
                                                stroke-width="0.5" id="v-47R" opacity="1">
                                            </polygon>
                                            <polygon points="0,0 5,5 5,15 0,20" fill="white" class="gigi hover"
                                                accesskey="47L" stroke="navy" stroke-width="0.5" id="v-47L"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,5 15,5 15,15 5,15" fill="white"
                                                class="gigi hover" accesskey="47C" stroke="navy"
                                                stroke-width="0.5" id="v-47C" opacity="1">
                                            </polygon>
                                            <text id="text-47" x="7" y="12" fill="black"
                                                style="font-size: 4pt;font-weight:bold"></text>
                                        </g>
                                    </svg>

                                </div>
                                <input type="text" value="0" id="gigi-47T" name="gigi-47T" hidden>
                                <input type="text" value="0" id="gigi-47B" name="gigi-47B" hidden>
                                <input type="text" value="0" id="gigi-47R" name="gigi-47R" hidden>
                                <input type="text" value="0" id="gigi-47L" name="gigi-47L" hidden>
                                <input type="text" value="0" id="gigi-47C" name="gigi-47C" hidden>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-center">47</h6>
                        </div>
                    </td>
                    <td title="Gigi 46">
                        <div>
                            <div>
                                <div class="custom-button mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <g>
                                            <polygon points="0,0 20,0 15,5 5,5" fill="white" class="gigi hover"
                                                accesskey="46T" stroke="navy" stroke-width="0.5" id="v-46T"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,15 15,15 20,20 0,20" fill="white"
                                                class="gigi hover" accesskey="46B" stroke="navy"
                                                stroke-width="0.5" id="v-46B" opacity="1">
                                            </polygon>
                                            <polygon points="15,5 20,0 20,20 15,15" fill="white"
                                                class="gigi hover" accesskey="46R" stroke="navy"
                                                stroke-width="0.5" id="v-46R" opacity="1">
                                            </polygon>
                                            <polygon points="0,0 5,5 5,15 0,20" fill="white" class="gigi hover"
                                                accesskey="46L" stroke="navy" stroke-width="0.5" id="v-46L"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,5 15,5 15,15 5,15" fill="white"
                                                class="gigi hover" accesskey="46C" stroke="navy"
                                                stroke-width="0.5" id="v-46C" opacity="1">
                                            </polygon>
                                            <text id="text-46" x="7" y="12" fill="black"
                                                style="font-size: 4pt;font-weight:bold"></text>
                                        </g>
                                    </svg>

                                </div>
                                <input type="text" value="0" id="gigi-46T" name="gigi-46T" hidden>
                                <input type="text" value="0" id="gigi-46B" name="gigi-46B" hidden>
                                <input type="text" value="0" id="gigi-46R" name="gigi-46R" hidden>
                                <input type="text" value="0" id="gigi-46L" name="gigi-46L" hidden>
                                <input type="text" value="0" id="gigi-46C" name="gigi-46C" hidden>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-center">46</h6>
                        </div>
                    </td>
                    <td title="Gigi 45">
                        <div>
                            <div>
                                <div class="custom-button mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <g>
                                            <polygon points="0,0 20,0 15,5 5,5" fill="white" class="gigi hover"
                                                accesskey="45T" stroke="navy" stroke-width="0.5" id="v-45T"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,15 15,15 20,20 0,20" fill="white"
                                                class="gigi hover" accesskey="45B" stroke="navy"
                                                stroke-width="0.5" id="v-45B" opacity="1">
                                            </polygon>
                                            <polygon points="15,5 20,0 20,20 15,15" fill="white"
                                                class="gigi hover" accesskey="45R" stroke="navy"
                                                stroke-width="0.5" id="v-45R" opacity="1">
                                            </polygon>
                                            <polygon points="0,0 5,5 5,15 0,20" fill="white" class="gigi hover"
                                                accesskey="45L" stroke="navy" stroke-width="0.5" id="v-45L"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,5 15,5 15,15 5,15" fill="white"
                                                class="gigi hover" accesskey="45C" stroke="navy"
                                                stroke-width="0.5" id="v-45C" opacity="1">
                                            </polygon>
                                            <text id="text-45" x="7" y="12" fill="black"
                                                style="font-size: 4pt;font-weight:bold"></text>
                                        </g>
                                    </svg>

                                </div>
                                <input type="text" value="0" id="gigi-45T" name="gigi-45T" hidden>
                                <input type="text" value="0" id="gigi-45B" name="gigi-45B" hidden>
                                <input type="text" value="0" id="gigi-45R" name="gigi-45R" hidden>
                                <input type="text" value="0" id="gigi-45L" name="gigi-45L" hidden>
                                <input type="text" value="0" id="gigi-45C" name="gigi-45C" hidden>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-center">45</h6>
                        </div>
                    </td>
                    <td title="Gigi 44">
                        <div>
                            <div>
                                <div class="custom-button mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <g>
                                            <polygon points="0,0 20,0 15,5 5,5" fill="white" class="gigi hover"
                                                accesskey="44T" stroke="navy" stroke-width="0.5" id="v-44T"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,15 15,15 20,20 0,20" fill="white"
                                                class="gigi hover" accesskey="44B" stroke="navy"
                                                stroke-width="0.5" id="v-44B" opacity="1">
                                            </polygon>
                                            <polygon points="15,5 20,0 20,20 15,15" fill="white"
                                                class="gigi hover" accesskey="44R" stroke="navy"
                                                stroke-width="0.5" id="v-44R" opacity="1">
                                            </polygon>
                                            <polygon points="0,0 5,5 5,15 0,20" fill="white" class="gigi hover"
                                                accesskey="44L" stroke="navy" stroke-width="0.5" id="v-44L"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,5 15,5 15,15 5,15" fill="white"
                                                class="gigi hover" accesskey="44C" stroke="navy"
                                                stroke-width="0.5" id="v-44C" opacity="1">
                                            </polygon>
                                            <text id="text-44" x="7" y="12" fill="black"
                                                style="font-size: 4pt;font-weight:bold"></text>
                                        </g>
                                    </svg>

                                </div>
                                <input type="text" value="0" id="gigi-44T" name="gigi-44T" hidden>
                                <input type="text" value="0" id="gigi-44B" name="gigi-44B" hidden>
                                <input type="text" value="0" id="gigi-44R" name="gigi-44R" hidden>
                                <input type="text" value="0" id="gigi-44L" name="gigi-44L" hidden>
                                <input type="text" value="0" id="gigi-44C" name="gigi-44C" hidden>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-center">44</h6>
                        </div>
                    </td>
                    <td title="Gigi 43">
                        <div>
                            <div>
                                <div class="custom-button mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <g>
                                            <polygon points="0,0 20,0 15,5 5,5" fill="white" class="gigi hover"
                                                accesskey="43T" stroke="navy" stroke-width="0.5" id="v-43T"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,15 15,15 20,20 0,20" fill="white"
                                                class="gigi hover" accesskey="43B" stroke="navy"
                                                stroke-width="0.5" id="v-43B" opacity="1">
                                            </polygon>
                                            <polygon points="15,5 20,0 20,20 15,15" fill="white"
                                                class="gigi hover" accesskey="43R" stroke="navy"
                                                stroke-width="0.5" id="v-43R" opacity="1">
                                            </polygon>
                                            <polygon points="0,0 5,5 5,15 0,20" fill="white" class="gigi hover"
                                                accesskey="43L" stroke="navy" stroke-width="0.5" id="v-43L"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,5 15,5 15,15 5,15" fill="white"
                                                class="gigi hover" accesskey="43C" stroke="navy"
                                                stroke-width="0.5" id="v-43C" opacity="1">
                                            </polygon>
                                            <text id="text-43" x="7" y="12" fill="black"
                                                style="font-size: 4pt;font-weight:bold"></text>
                                        </g>
                                    </svg>

                                </div>
                                <input type="text" value="0" id="gigi-43T" name="gigi-43T" hidden>
                                <input type="text" value="0" id="gigi-43B" name="gigi-43B" hidden>
                                <input type="text" value="0" id="gigi-43R" name="gigi-43R" hidden>
                                <input type="text" value="0" id="gigi-43L" name="gigi-43L" hidden>
                                <input type="text" value="0" id="gigi-43C" name="gigi-43C" hidden>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-center">43</h6>
                        </div>
                    </td>
                    <td title="Gigi 42">
                        <div>
                            <div>
                                <div class="custom-button mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <g>
                                            <polygon points="0,0 20,0 15,5 5,5" fill="white" class="gigi hover"
                                                accesskey="42T" stroke="navy" stroke-width="0.5" id="v-42T"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,15 15,15 20,20 0,20" fill="white"
                                                class="gigi hover" accesskey="42B" stroke="navy"
                                                stroke-width="0.5" id="v-42B" opacity="1">
                                            </polygon>
                                            <polygon points="15,5 20,0 20,20 15,15" fill="white"
                                                class="gigi hover" accesskey="42R" stroke="navy"
                                                stroke-width="0.5" id="v-42R" opacity="1">
                                            </polygon>
                                            <polygon points="0,0 5,5 5,15 0,20" fill="white" class="gigi hover"
                                                accesskey="42L" stroke="navy" stroke-width="0.5" id="v-42L"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,5 15,5 15,15 5,15" fill="white"
                                                class="gigi hover" accesskey="42C" stroke="navy"
                                                stroke-width="0.5" id="v-42C" opacity="1">
                                            </polygon>
                                            <text id="text-42" x="7" y="12" fill="black"
                                                style="font-size: 4pt;font-weight:bold"></text>
                                        </g>
                                    </svg>

                                </div>
                                <input type="text" value="0" id="gigi-42T" name="gigi-42T" hidden>
                                <input type="text" value="0" id="gigi-42B" name="gigi-42B" hidden>
                                <input type="text" value="0" id="gigi-42R" name="gigi-42R" hidden>
                                <input type="text" value="0" id="gigi-42L" name="gigi-42L" hidden>
                                <input type="text" value="0" id="gigi-42C" name="gigi-42C" hidden>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-center">42</h6>
                        </div>
                    </td>
                    <td title="Gigi 41">
                        <div>
                            <div>
                                <div class="custom-button mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <g>
                                            <polygon points="0,0 20,0 15,5 5,5" fill="white" class="gigi hover"
                                                accesskey="41T" stroke="navy" stroke-width="0.5" id="v-41T"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,15 15,15 20,20 0,20" fill="white"
                                                class="gigi hover" accesskey="41B" stroke="navy"
                                                stroke-width="0.5" id="v-41B" opacity="1">
                                            </polygon>
                                            <polygon points="15,5 20,0 20,20 15,15" fill="white"
                                                class="gigi hover" accesskey="41R" stroke="navy"
                                                stroke-width="0.5" id="v-41R" opacity="1">
                                            </polygon>
                                            <polygon points="0,0 5,5 5,15 0,20" fill="white" class="gigi hover"
                                                accesskey="41L" stroke="navy" stroke-width="0.5" id="v-41L"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,5 15,5 15,15 5,15" fill="white"
                                                class="gigi hover" accesskey="41C" stroke="navy"
                                                stroke-width="0.5" id="v-41C" opacity="1">
                                            </polygon>
                                            <text id="text-41" x="7" y="12" fill="black"
                                                style="font-size: 4pt;font-weight:bold"></text>
                                        </g>
                                    </svg>

                                </div>
                                <input type="text" value="0" id="gigi-41T" name="gigi-41T" hidden>
                                <input type="text" value="0" id="gigi-41B" name="gigi-41B" hidden>
                                <input type="text" value="0" id="gigi-41R" name="gigi-41R" hidden>
                                <input type="text" value="0" id="gigi-41L" name="gigi-41L" hidden>
                                <input type="text" value="0" id="gigi-41C" name="gigi-41C" hidden>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-center">41</h6>
                        </div>
                    </td>
                    <td class="y"></td>
                    <td title="Gigi 31">
                        <div>
                            <div>
                                <div class="custom-button mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <g>
                                            <polygon points="0,0 20,0 15,5 5,5" fill="white" class="gigi hover"
                                                accesskey="31T" stroke="navy" stroke-width="0.5" id="v-31T"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,15 15,15 20,20 0,20" fill="white"
                                                class="gigi hover" accesskey="31B" stroke="navy"
                                                stroke-width="0.5" id="v-31B" opacity="1">
                                            </polygon>
                                            <polygon points="15,5 20,0 20,20 15,15" fill="white"
                                                class="gigi hover" accesskey="31R" stroke="navy"
                                                stroke-width="0.5" id="v-31R" opacity="1">
                                            </polygon>
                                            <polygon points="0,0 5,5 5,15 0,20" fill="white" class="gigi hover"
                                                accesskey="31L" stroke="navy" stroke-width="0.5" id="v-31L"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,5 15,5 15,15 5,15" fill="white"
                                                class="gigi hover" accesskey="31C" stroke="navy"
                                                stroke-width="0.5" id="v-31C" opacity="1">
                                            </polygon>
                                            <text id="text-31" x="7" y="12" fill="black"
                                                style="font-size: 4pt;font-weight:bold"></text>
                                        </g>
                                    </svg>

                                </div>
                                <input type="text" value="0" id="gigi-31T" name="gigi-31T" hidden>
                                <input type="text" value="0" id="gigi-31B" name="gigi-31B" hidden>
                                <input type="text" value="0" id="gigi-31R" name="gigi-31R" hidden>
                                <input type="text" value="0" id="gigi-31L" name="gigi-31L" hidden>
                                <input type="text" value="0" id="gigi-31C" name="gigi-31C" hidden>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-center">31</h6>
                        </div>
                    </td>
                    <td title="Gigi 32">
                        <div>
                            <div>
                                <div class="custom-button mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <g>
                                            <polygon points="0,0 20,0 15,5 5,5" fill="white" class="gigi hover"
                                                accesskey="32T" stroke="navy" stroke-width="0.5" id="v-32T"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,15 15,15 20,20 0,20" fill="white"
                                                class="gigi hover" accesskey="32B" stroke="navy"
                                                stroke-width="0.5" id="v-32B" opacity="1">
                                            </polygon>
                                            <polygon points="15,5 20,0 20,20 15,15" fill="white"
                                                class="gigi hover" accesskey="32R" stroke="navy"
                                                stroke-width="0.5" id="v-32R" opacity="1">
                                            </polygon>
                                            <polygon points="0,0 5,5 5,15 0,20" fill="white" class="gigi hover"
                                                accesskey="32L" stroke="navy" stroke-width="0.5" id="v-32L"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,5 15,5 15,15 5,15" fill="white"
                                                class="gigi hover" accesskey="32C" stroke="navy"
                                                stroke-width="0.5" id="v-32C" opacity="1">
                                            </polygon>
                                            <text id="text-32" x="7" y="12" fill="black"
                                                style="font-size: 4pt;font-weight:bold"></text>
                                        </g>
                                    </svg>

                                </div>
                                <input type="text" value="0" id="gigi-32T" name="gigi-32T" hidden>
                                <input type="text" value="0" id="gigi-32B" name="gigi-32B" hidden>
                                <input type="text" value="0" id="gigi-32R" name="gigi-32R" hidden>
                                <input type="text" value="0" id="gigi-32L" name="gigi-32L" hidden>
                                <input type="text" value="0" id="gigi-32C" name="gigi-32C" hidden>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-center">32</h6>
                        </div>
                    </td>
                    <td title="Gigi 33">
                        <div>
                            <div>
                                <div class="custom-button mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <g>
                                            <polygon points="0,0 20,0 15,5 5,5" fill="white" class="gigi hover"
                                                accesskey="33T" stroke="navy" stroke-width="0.5" id="v-33T"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,15 15,15 20,20 0,20" fill="white"
                                                class="gigi hover" accesskey="33B" stroke="navy"
                                                stroke-width="0.5" id="v-33B" opacity="1">
                                            </polygon>
                                            <polygon points="15,5 20,0 20,20 15,15" fill="white"
                                                class="gigi hover" accesskey="33R" stroke="navy"
                                                stroke-width="0.5" id="v-33R" opacity="1">
                                            </polygon>
                                            <polygon points="0,0 5,5 5,15 0,20" fill="white" class="gigi hover"
                                                accesskey="33L" stroke="navy" stroke-width="0.5" id="v-33L"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,5 15,5 15,15 5,15" fill="white"
                                                class="gigi hover" accesskey="33C" stroke="navy"
                                                stroke-width="0.5" id="v-33C" opacity="1">
                                            </polygon>
                                            <text id="text-33" x="7" y="12" fill="black"
                                                style="font-size: 4pt;font-weight:bold"></text>
                                        </g>
                                    </svg>

                                </div>
                                <input type="text" value="0" id="gigi-33T" name="gigi-33T" hidden>
                                <input type="text" value="0" id="gigi-33B" name="gigi-33B" hidden>
                                <input type="text" value="0" id="gigi-33R" name="gigi-33R" hidden>
                                <input type="text" value="0" id="gigi-33L" name="gigi-33L" hidden>
                                <input type="text" value="0" id="gigi-33C" name="gigi-33C" hidden>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-center">33</h6>
                        </div>
                    </td>
                    <td title="Gigi 34">
                        <div>
                            <div>
                                <div class="custom-button mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <g>
                                            <polygon points="0,0 20,0 15,5 5,5" fill="white" class="gigi hover"
                                                accesskey="34T" stroke="navy" stroke-width="0.5" id="v-34T"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,15 15,15 20,20 0,20" fill="white"
                                                class="gigi hover" accesskey="34B" stroke="navy"
                                                stroke-width="0.5" id="v-34B" opacity="1">
                                            </polygon>
                                            <polygon points="15,5 20,0 20,20 15,15" fill="white"
                                                class="gigi hover" accesskey="34R" stroke="navy"
                                                stroke-width="0.5" id="v-34R" opacity="1">
                                            </polygon>
                                            <polygon points="0,0 5,5 5,15 0,20" fill="white" class="gigi hover"
                                                accesskey="34L" stroke="navy" stroke-width="0.5" id="v-34L"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,5 15,5 15,15 5,15" fill="white"
                                                class="gigi hover" accesskey="34C" stroke="navy"
                                                stroke-width="0.5" id="v-34C" opacity="1">
                                            </polygon>
                                            <text id="text-34" x="7" y="12" fill="black"
                                                style="font-size: 4pt;font-weight:bold"></text>
                                        </g>
                                    </svg>

                                </div>
                                <input type="text" value="0" id="gigi-34T" name="gigi-34T" hidden>
                                <input type="text" value="0" id="gigi-34B" name="gigi-34B" hidden>
                                <input type="text" value="0" id="gigi-34R" name="gigi-34R" hidden>
                                <input type="text" value="0" id="gigi-34L" name="gigi-34L" hidden>
                                <input type="text" value="0" id="gigi-34C" name="gigi-34C" hidden>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-center">34</h6>
                        </div>
                    </td>
                    <td title="Gigi 35">
                        <div>
                            <div>
                                <div class="custom-button mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <g>
                                            <polygon points="0,0 20,0 15,5 5,5" fill="white" class="gigi hover"
                                                accesskey="35T" stroke="navy" stroke-width="0.5" id="v-35T"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,15 15,15 20,20 0,20" fill="white"
                                                class="gigi hover" accesskey="35B" stroke="navy"
                                                stroke-width="0.5" id="v-35B" opacity="1">
                                            </polygon>
                                            <polygon points="15,5 20,0 20,20 15,15" fill="white"
                                                class="gigi hover" accesskey="35R" stroke="navy"
                                                stroke-width="0.5" id="v-35R" opacity="1">
                                            </polygon>
                                            <polygon points="0,0 5,5 5,15 0,20" fill="white" class="gigi hover"
                                                accesskey="35L" stroke="navy" stroke-width="0.5" id="v-35L"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,5 15,5 15,15 5,15" fill="white"
                                                class="gigi hover" accesskey="35C" stroke="navy"
                                                stroke-width="0.5" id="v-35C" opacity="1">
                                            </polygon>
                                            <text id="text-35" x="7" y="12" fill="black"
                                                style="font-size: 4pt;font-weight:bold"></text>
                                        </g>
                                    </svg>

                                </div>
                                <input type="text" value="0" id="gigi-35T" name="gigi-35T" hidden>
                                <input type="text" value="0" id="gigi-35B" name="gigi-35B" hidden>
                                <input type="text" value="0" id="gigi-35R" name="gigi-35R" hidden>
                                <input type="text" value="0" id="gigi-35L" name="gigi-35L" hidden>
                                <input type="text" value="0" id="gigi-35C" name="gigi-35C" hidden>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-center">35</h6>
                        </div>
                    </td>
                    <td title="Gigi 36">
                        <div>
                            <div>
                                <div class="custom-button mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <g>
                                            <polygon points="0,0 20,0 15,5 5,5" fill="white" class="gigi hover"
                                                accesskey="36T" stroke="navy" stroke-width="0.5" id="v-36T"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,15 15,15 20,20 0,20" fill="white"
                                                class="gigi hover" accesskey="36B" stroke="navy"
                                                stroke-width="0.5" id="v-36B" opacity="1">
                                            </polygon>
                                            <polygon points="15,5 20,0 20,20 15,15" fill="white"
                                                class="gigi hover" accesskey="36R" stroke="navy"
                                                stroke-width="0.5" id="v-36R" opacity="1">
                                            </polygon>
                                            <polygon points="0,0 5,5 5,15 0,20" fill="white" class="gigi hover"
                                                accesskey="36L" stroke="navy" stroke-width="0.5" id="v-36L"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,5 15,5 15,15 5,15" fill="white"
                                                class="gigi hover" accesskey="36C" stroke="navy"
                                                stroke-width="0.5" id="v-36C" opacity="1">
                                            </polygon>
                                            <text id="text-36" x="7" y="12" fill="black"
                                                style="font-size: 4pt;font-weight:bold"></text>
                                        </g>
                                    </svg>

                                </div>
                                <input type="text" value="0" id="gigi-36T" name="gigi-36T" hidden>
                                <input type="text" value="0" id="gigi-36B" name="gigi-36B" hidden>
                                <input type="text" value="0" id="gigi-36R" name="gigi-36R" hidden>
                                <input type="text" value="0" id="gigi-36L" name="gigi-36L" hidden>
                                <input type="text" value="0" id="gigi-36C" name="gigi-36C" hidden>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-center">36</h6>
                        </div>
                    </td>
                    <td title="Gigi 37">
                        <div>
                            <div>
                                <div class="custom-button mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <g>
                                            <polygon points="0,0 20,0 15,5 5,5" fill="white" class="gigi hover"
                                                accesskey="37T" stroke="navy" stroke-width="0.5" id="v-37T"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,15 15,15 20,20 0,20" fill="white"
                                                class="gigi hover" accesskey="37B" stroke="navy"
                                                stroke-width="0.5" id="v-37B" opacity="1">
                                            </polygon>
                                            <polygon points="15,5 20,0 20,20 15,15" fill="white"
                                                class="gigi hover" accesskey="37R" stroke="navy"
                                                stroke-width="0.5" id="v-37R" opacity="1">
                                            </polygon>
                                            <polygon points="0,0 5,5 5,15 0,20" fill="white" class="gigi hover"
                                                accesskey="37L" stroke="navy" stroke-width="0.5" id="v-37L"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,5 15,5 15,15 5,15" fill="white"
                                                class="gigi hover" accesskey="37C" stroke="navy"
                                                stroke-width="0.5" id="v-37C" opacity="1">
                                            </polygon>
                                            <text id="text-37" x="7" y="12" fill="black"
                                                style="font-size: 4pt;font-weight:bold"></text>
                                        </g>
                                    </svg>

                                </div>
                                <input type="text" value="0" id="gigi-37T" name="gigi-37T" hidden>
                                <input type="text" value="0" id="gigi-37B" name="gigi-37B" hidden>
                                <input type="text" value="0" id="gigi-37R" name="gigi-37R" hidden>
                                <input type="text" value="0" id="gigi-37L" name="gigi-37L" hidden>
                                <input type="text" value="0" id="gigi-37C" name="gigi-37C" hidden>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-center">37</h6>
                        </div>
                    </td>
                    <td title="Gigi 38">
                        <div>
                            <div>
                                <div class="custom-button mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <g>
                                            <polygon points="0,0 20,0 15,5 5,5" fill="white" class="gigi hover"
                                                accesskey="38T" stroke="navy" stroke-width="0.5" id="v-38T"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,15 15,15 20,20 0,20" fill="white"
                                                class="gigi hover" accesskey="38B" stroke="navy"
                                                stroke-width="0.5" id="v-38B" opacity="1">
                                            </polygon>
                                            <polygon points="15,5 20,0 20,20 15,15" fill="white"
                                                class="gigi hover" accesskey="38R" stroke="navy"
                                                stroke-width="0.5" id="v-38R" opacity="1">
                                            </polygon>
                                            <polygon points="0,0 5,5 5,15 0,20" fill="white" class="gigi hover"
                                                accesskey="38L" stroke="navy" stroke-width="0.5" id="v-38L"
                                                opacity="1">
                                            </polygon>
                                            <polygon points="5,5 15,5 15,15 5,15" fill="white"
                                                class="gigi hover" accesskey="38C" stroke="navy"
                                                stroke-width="0.5" id="v-38C" opacity="1">
                                            </polygon>
                                            <text id="text-38" x="7" y="12" fill="black"
                                                style="font-size: 4pt;font-weight:bold"></text>
                                        </g>
                                    </svg>

                                </div>
                                <input type="text" value="0" id="gigi-38T" name="gigi-38T" hidden>
                                <input type="text" value="0" id="gigi-38B" name="gigi-38B" hidden>
                                <input type="text" value="0" id="gigi-38R" name="gigi-38R" hidden>
                                <input type="text" value="0" id="gigi-38L" name="gigi-38L" hidden>
                                <input type="text" value="0" id="gigi-38C" name="gigi-38C" hidden>
                            </div>
                        </div>
                        <div>
                            <h6 class="text-center">38</h6>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>


    <br>
    <div class="row">
        <div class="col-md-12">
            <div id="div-slot1"></div>
        </div>
        {{-- <div class="col-md-6">
                    <div id="div-slot2"></div>
                </div> --}}
    </div>
</form>
@if ($status->status == 'Di Periksa')
    <div class="modal fade" role="dialog" tabindex="-1" id="m-gigi-selected">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Pilih satu pilihan</h4><span class="ml-1">Gigi
                        <strong>0</strong></span>
                    <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button> -->
                </div>
                <div class="modal-body">
                    <div class="p-2">
                        <div class="row" role="group">
                            <div class="col-md-12">&nbsp;</div>
                            <div class="col-md-2  d-flex align-items-stretch">
                                <button class="btn btn-sm btn-outline-dark btn-c btn-block" type="button"
                                    accesskey="1" style="font-size: 12px;">UE</button><br>
                            </div>
                            <div class="col-md-2  d-flex align-items-stretch">
                                <button class="btn btn-sm btn-outline-dark btn-c btn-block" type="button"
                                    accesskey="2" style="font-size: 12px;">PE</button><br>
                            </div>
                            <div class="col-md-2  d-flex align-items-stretch">
                                <button class="btn btn-sm btn-outline-dark btn-c btn-block" type="button"
                                    accesskey="3" style="font-size: 12px;">A</button><br>
                            </div>
                            <div class="col-md-2  d-flex align-items-stretch">
                                <button class="btn btn-sm btn-outline-dark btn-c btn-block" type="button"
                                    accesskey="4" style="font-size: 12px;">√</button><br>
                            </div>
                            <div class="col-md-2  d-flex align-items-stretch">
                                <button class="btn btn-sm btn-outline-dark btn-c btn-block " type="button"
                                    accesskey="5" style="font-size: 12px;"><span
                                        class='text-danger'>X</span></button><br>
                            </div>
                            <div class="col-md-2  d-flex align-items-stretch">
                                <button class="btn btn-sm btn-outline-dark btn-c btn-block" type="button"
                                    accesskey="6" style="font-size: 12px;"><span
                                        class='text-success'>__</span></button><br>
                            </div>
                            <div class="col-md-12">
                                <hr>
                            </div>
                            <div class="col-md-2  d-flex align-items-stretch">
                                <button class="btn btn-sm btn-outline-dark btn-c btn-block" type="button"
                                    accesskey="7" style="font-size: 12px;"><span
                                        class='text-warning'>--</span></button><br>
                            </div>
                            <div class="col-md-2  d-flex align-items-stretch">
                                <button class="btn btn-sm btn-light btn-c btn-block" type="button" accesskey="8"
                                    style="font-size: 12px;"><span class='text-danger'>/</span></button><br>
                            </div>

                            <div class="col-md-2  d-flex w-flex align-items-stretch">
                                <button class="btn btn-sm btn-light btn-c btn-block" type="button" accesskey="9"
                                    style="background-color: grey;font-size: 12px;">&nbsp;</button><br>
                            </div>

                            <div class="col-md-2  d-flex align-items-stretch">
                                <button class="btn btn-sm btn-light btn-c btn-block" type="button" accesskey="10"
                                    style="background-color: pink;font-size: 12px;">&nbsp;</button><br>
                            </div>
                            <div class="col-md-2 d-flex align-items-stretch">
                                <button class="btn btn-sm btn-light btn-c btn-block" type="button" accesskey="11"
                                    style="background-color: cadetblue;font-size: 12px;">&nbsp;</button><br>
                            </div>

                            <div class="col-md-2  d-flex align-items-stretch">
                                <button class="btn btn-sm btn-light btn-c btn-block" type="button" accesskey="12"
                                    style="background-color: darkgreen;font-size: 12px;">&nbsp;</button><br>
                            </div>
                            <div class="col-md-12">
                                <hr>
                            </div>
                            <div class="col-md-2  d-flex align-items-stretch">
                                <button class="btn btn-sm btn-light btn-c btn-block" type="button" accesskey="13"
                                    style="background-color: cyan;font-size: 12px;">&nbsp;</button><br>
                            </div>



                            <div class="col-md-2  d-flex align-items-stretch">
                                <button class="btn btn-sm btn-dark btn-c btn-block" type="button" accesskey="14"
                                    style="font-size: 12px;">&nbsp;</button><br>
                            </div>


                            <div class="col-md-2  d-flex align-items-stretch">
                                <button class="btn btn-sm btn-dark btn-c btn-block" type="button" accesskey="14"
                                    style="font-size: 12px;">&nbsp;</button><br>
                            </div>
                            <div class="col-md-12 text-center">
                                <hr>
                                <div class="float-end">
                                    <a id="add-send" class="btn btn-primary btn-sm rounded-pill "
                                        data-bs-dismiss="modal"><i class="fa fa-save"></i> Simpan</a>
                                    <button class="btn btn-sm rounded-pill btn-danger btn-c " type="button"
                                        accesskey="0"><i class="fa fa-trash"></i>
                                        Reset</button><br>
                                </div>
                            </div>
                            <div class="col-md-12 text-center"></div>
                        </div>
                    </div>
                </div>
                <!-- <div class=" modal-footer"><button class="btn btn-light" type="button" data-dismiss="modal">Batal</button> -->
            </div>
        </div>
    </div>
@endif
</section>
<!-- /.content -->
@if ($status->status == 'Di Periksa')
    <script>
        $('#save-data').on('click', function() {
            $.ajax({
                url: "<?= url('pemeriksaan/pasien/' . $id) ?>/odontogram/save",
                type: 'POST',
                data: {
                    mapping: JSON.stringify(selectedData),
                    odontotype: "{{ $odontotype }}",
                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    Toast.fire({
                        icon: "success",
                        title: "Data berhasil disimpan"
                    });
                },
                error: function(xhr) {
                    Swal.close();
                    const msg = xhr.responseJSON?.error || 'Terjadi kesalahan.';
                    Toast.fire({
                        icon: "error",
                        title: msg
                    });
                },
            });
        })
    </script>
@endif
<script>
    var kondisiGigiList = [];

    $(document).ready(function() {
        getDiagnosisList()
    });

    function getDiagnosisList() {
        $.ajax({
            url: "<?= url('pemeriksaan/pasien/' . $id) ?>/odontogram/diagnosisList",
            type: "POST",
            data: {
                kategori: "2d",
            },
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
            },
            success: function(response) {
                kondisiGigiList = response.data || [];
                getDataOdonto();
            },
            error: function() {
                kondisiGigiList = [];
                getDataOdonto();
            },
        });
    }

    function getDataOdonto() {
        $.ajax({
            url: "<?= url('pemeriksaan/pasien/' . $id) ?>/odontogram/loadOdonto",
            type: "POST",
            data: {
                odontotype: "{{ $odontotype }}",
            },
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
            },
            success: function(response) {
                let data = [];
                if (response.data && response.data.mappeddata) {
                    data = JSON.parse(response.data.mappeddata);
                }
                load2DGlobal(data, kondisiGigiList);
            },
            error: function(xhr) {
                Swal.close();
                const msg = xhr.responseJSON?.error || "Terjadi kesalahan.";
                Toast.fire({
                    icon: "error",
                    title: msg,
                });
            },
        });
    }
</script>
