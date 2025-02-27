<form class="form-horizontal" method="POST" action="nilai_mahasiswa.php">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"> 
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

<div class="container">
  <div class="border p-3 bg-light">
    <h5 class="mb-3 h6">Sistem Penilaian</h5>
    <hr/>
    <h6 class="mb-4 h4">Form Nilai Siswa</h6>
    
    <form>
      <div class="form-group row">
        <label for="nama_lengkap" class="col-4 col-form-label text-right">Nama Lengkap</label> 
        <div class="col-8">
          <input id="nama_lengkap" name="nama_lengkap" placeholder="Nama Lengkap" type="text" class="form-control">
        </div>
      </div>
      <div class="form-group row">
        <label for="mata_kuliah" class="col-4 col-form-label text-right">Mata Kuliah</label> 
        <div class="col-8">
          <select id="mata_kuliah" name="mata_kuliah" class="form-control">
            <option value="Dasar-Dasar Pemrogramman">Dasar Dasar Pemrograman</option>
            <option value="Basis Data">Basis Data</option>
            <option value="Pemrograman Web">Pemrograman Web</option>
          </select>
        </div>
      </div>
      <div class="form-group row">
        <label for="nilai_uts" class="col-4 col-form-label text-right">Nilai UTS</label> 
        <div class="col-8">
          <input id="nilai_uts" name="nilai_uts" placeholder="Nilai UTS" type="text" class="form-control">
        </div>
      </div>
      <div class="form-group row">
        <label for="nilai_uas" class="col-4 col-form-label text-right">Nilai UAS</label> 
        <div class="col-8">
          <input id="nilai_uas" name="nilai_uas" placeholder="Nilai UAS" type="text" class="form-control">
        </div>
      </div>
      <div class="form-group row">
        <label for="nilai_tugas" class="col-4 col-form-label text-right">Nilai Tugas/Praktikum</label> 
        <div class="col-8">
          <input id="nilai_tugas" name="nilai_tugas" placeholder="Nilai Tugas" type="text" class="form-control">
        </div>
      </div> 
      <div class="form-group row">
        <div class="offset-4 col-8">
          <button name="simpan" type="submit" class="btn btn-primary" value="Proses">Simpan</button>
        </div>
      </div>
    </form>
    </div>
</div>