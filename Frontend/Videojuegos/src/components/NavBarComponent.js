import { Link } from 'react-router-dom';
import './Estilos.css';



export default function NavBar({onResetFiltros}){
    return(
        <nav>
            <ul className='navegacion'>
            <li><Link to="/" onClick={onResetFiltros}>inicio</Link></li>
            </ul>
        </nav>
        )
}


