import { Link } from "react-router-dom";

const handleLogout = () => {
  localStorage.removeItem('token');
  localStorage.removeItem('nombre_usuario');
  localStorage.removeItem("token-time");
};

export default function Login() {
  const tiempotoken = localStorage.getItem("token-time");
  const token = localStorage.getItem("token");
  const tiempoact = new Date().getTime();

  if (!token || !tiempotoken || tiempotoken < tiempoact) {
    return (
        <button className="loginbutton">
          <Link to='/login'>Login</Link>
        </button>

    );
  } else {
    return (
      <div className="header-links">
        <h2>{localStorage.getItem("nombre_usuario")}</h2>
        <button  onClick={handleLogout}>
          <Link to='/login'>Log out</Link>
        </button>
      </div>
    );
  }
}
