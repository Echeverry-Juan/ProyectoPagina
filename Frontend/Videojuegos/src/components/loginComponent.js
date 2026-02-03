import { Link } from "react-router-dom";

const handleLogout = () => {
  localStorage.removeItem('token');
  localStorage.removeItem('nombre_usuario');
  localStorage.removeItem("token-time");
  localStorage.removeItem("es_admin");
};

export default function Login() {
  const tiempotoken = localStorage.getItem("token-time");
  const token = localStorage.getItem("token");
  const tiempoact = new Date().getTime();
  const es_admin= localStorage.getItem("es_admin");
  if (!token || !tiempotoken || tiempotoken < tiempoact) {
    return (
        <button className="loginbutton">
          <Link to='/login'>Login</Link>
        </button>

    );
  } else {
    if(es_admin == '1'){
    return (
      <div className="header-links">
        <h2>{localStorage.getItem("nombre_usuario")}</h2>
        <p><Link to='/Admin'>Opciones de Administrador</Link></p>
        <button  onClick={handleLogout}>
          <Link to='/login'>Log out</Link>
        </button>
      </div>
    );
    }else{
      return(
        <div className="header-links">
        <h2>{localStorage.getItem("nombre_usuario")}</h2>
        <button  onClick={handleLogout}>
          <Link to='/login'>Log out</Link>
        </button>
      </div>
      )
    }
  }
}
