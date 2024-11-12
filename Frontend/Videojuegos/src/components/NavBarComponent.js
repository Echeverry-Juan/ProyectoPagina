import { Link } from 'react-router-dom';
import './Estilos.css';

export default function NavBar(){
    return(
        <nav>
            <ul className='navegacion'>
            <li ><Link  to="/">Inicio</Link></li>
            <li><Link to="/juegos">Mis Calificaciones</Link></li>
            <li><Link to="/juegos">Perfil</Link></li>
            </ul>
        </nav>
    )
}


