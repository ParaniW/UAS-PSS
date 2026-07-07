<x-layouts.app title="Daftar Poli">

    <div class="max-w-4xl mx-auto space-y-8">

        {{-- FORM PENDAFTARAN --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8">

            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-slate-800">
                    Pendaftaran Poli
                </h1>

                <p class="text-slate-500 mt-2">
                    Silakan pilih poli dan jadwal pemeriksaan.
                </p>
            </div>

            @if(session('message'))
                <div class="alert alert-success mb-5">
                    {{ session('message') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-error mb-5">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('pasien.daftar-poli.store') }}" method="POST">
                @csrf

                <div class="mb-5">
                    <label class="font-semibold">
                        Nomor Rekam Medis
                    </label>

                    <input
                        type="text"
                        value="{{ $user->no_rm }}"
                        readonly
                        class="input input-bordered w-full mt-2">
                </div>

                <div class="grid md:grid-cols-2 gap-5">

                    <div>
                        <label class="font-semibold">
                            Poli
                        </label>

                       <select name="id_poli" class="select select-bordered w-full mt-2" required>

    <option value="">-- Pilih Poli --</option>

    @foreach($polis as $item)

        <option value="{{ $item->id }}">
            {{ $item->nama_poli }}
        </option>

    @endforeach

</select>
                    </div>

                    <div>

                        <label class="font-semibold">
                            Jadwal Periksa
                        </label>

                        <select
                            id="id_jadwal"
                            name="id_jadwal"
                            class="select select-bordered w-full mt-2"
                            required>

                            <option value="">-- Pilih Jadwal --</option>

                            @foreach($jadwals as $jadwal)

                                <option
                                    value="{{ $jadwal->id }}"
                                    data-poli="{{ $jadwal->dokter->id_poli }}">

                                    {{ $jadwal->dokter->poli->kode_poli }}
                                    |
                                    {{ $jadwal->hari }}
                                    |
                                    {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}
                                    -
                                    {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                    |
                                    Dr. {{ $jadwal->dokter->nama }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                </div>

                <div class="mt-5">

                    <label class="font-semibold">
                        Keluhan
                    </label>

                    <textarea
                        name="keluhan"
                        rows="4"
                        class="textarea textarea-bordered w-full mt-2"
                        required>{{ old('keluhan') }}</textarea>

                </div>

                <div class="mt-6">

                    <button
                        class="btn btn-primary">

                        Daftar Poli

                    </button>

                </div>

            </form>

        </div>

        {{-- STATUS ANTRIAN --}}

        <div class="bg-white rounded-3xl shadow-sm border">

            <div class="p-5 border-b">
                <h2 class="font-bold text-xl">
                    Status Antrian
                </h2>
            </div>

            <div class="overflow-x-auto">

                <table class="table">

                    <thead>

                        <tr>

                            <th>Poli</th>
                            <th>Dokter</th>
                            <th>Hari</th>
                            <th>Antrian</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($jadwals as $j)

                            <tr>

                                <td>

                                    {{ $j->dokter->poli->nama_poli }}

                                </td>

                                <td>

                                    Dr. {{ $j->dokter->nama }}

                                </td>

                                <td>

                                    {{ $j->hari }}

                                    <br>

                                    {{ \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') }}

                                    -

                                    {{ \Carbon\Carbon::parse($j->jam_selesai)->format('H:i') }}

                                </td>

                                <td>

                                    {{ $j->dokter->poli->kode_poli }}

                                    -

                                    {{ $j->current_antrian ?? 0 }}

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="4" class="text-center">

                                    Belum ada jadwal.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

    <script>

        const poliSelect=document.getElementById('id_poli');
        const jadwalSelect=document.getElementById('id_jadwal');

        const semuaOption=[...jadwalSelect.options];

        poliSelect.addEventListener('change',function(){

            const idPoli=this.value;

            jadwalSelect.innerHTML='';

            semuaOption.forEach(function(option){

                if(option.value===""){

                    jadwalSelect.appendChild(option.cloneNode(true));

                    return;

                }

                if(option.dataset.poli===idPoli){

                    jadwalSelect.appendChild(option.cloneNode(true));

                }

            });

        });

    </script>

</x-layouts.app>
