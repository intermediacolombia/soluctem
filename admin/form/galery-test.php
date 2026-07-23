<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Prueba Lightbox</title>

  <!-- CSS de Lightbox -->
  <link
    href="../css/lightbox.min.css" rel="stylesheet"
  />
  <style>
    .image-container img {
      max-width: 150px;
      margin: 5px;
      cursor: pointer;
      border: 2px solid #ccc;
      border-radius: 4px;
      transition: border-color 0.3s;
    }
    .image-container img:hover {
      border-color: #5fca00;
    }
  </style>
</head>
<body>

  <h2>Imágenes Asociadas</h2>
  <div>
      <a class="example-image-link" href="http://lokeshdhakar.com/projects/lightbox2/images/image-1.jpg" data-lightbox="example-1"><img class="example-image" src="http://lokeshdhakar.com/projects/lightbox2/images/thumb-1.jpg" alt="image-1" /></a>
      <a class="example-image-link" href="http://lokeshdhakar.com/projects/lightbox2/images/image-2.jpg" data-lightbox="example-2" data-title="Optional caption."><img class="example-image" src="http://lokeshdhakar.com/projects/lightbox2/images/thumb-2.jpg" alt="image-1"/></a>
    </div>
	


  <!-- JS de Lightbox -->
  <script src="../js/lightbox-plus-jquery.min.js"></script>
</body>
</html>
