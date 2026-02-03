import { Link } from "react-router-dom";
import './JuegoCard.css';


function JuegoCard(props) {
  const { juego } = props;
  const imagenSrc = juego.imagen && juego.imagen.startsWith("data:image/jpeg;base64,")
  //cambios
  ? juego.imagen
  : juego.imagen
  ? `data:image/jpeg;base64,${juego.imagen}`
  : "https://i.pinimg.com/564x/0c/9f/60/0c9f602f65c3070924ba72d25c51700d.jpg";

  
  const calificacionprom=()=>{
    if(juego.puntuacion_promedio>0){
      return(<span className="juego-card-calificacion">
        Calificación promedio: <span>{juego.puntuacion_promedio}</span> / 5
      </span>)
    }else{
      return(<span className="juego-card-calificacion">
        Sin calificaciones por el momento
      </span>)
    }
  }

  const juego_carta =     
      <div className="juego-card">
        <div className="juego-card-imagen">
          <img src={imagenSrc} alt={juego.nombre} />
        </div>
        <div className="juego-card-content">
          <h3 className="juego-card-title">
            <Link to={`/juegos/${juego.id}`}>{juego.nombre}</Link>
          </h3>
          <p className="juego-card-description">{juego.descripcion}</p>
          <div className="juego-card-info">
            <span className="juego-card-age">{juego.clasificacion_edad}</span>
            <span className="juego-card-platform">{juego.plataformas}</span>
          </div>
          <div className="juego-card-footer">
            {calificacionprom()}
          </div>
        </div>
      </div>;

  return (
    <div className="contenedor-juego">
      {juego_carta}
    </div>
  );
}

export default JuegoCard;

