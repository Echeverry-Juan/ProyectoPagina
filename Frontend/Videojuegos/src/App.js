import './App.css';
import Footer from './components/FooterComponent';

import { BrowserRouter as Router, Routes, Route } from "react-router-dom";
import JuegoPage from './pages/juego/JuegoPage';
import JuegoDetalles from './pages/juego/JuegoDetalles';
import LoginPage from './pages/registro/LoginPage';
import RegistroPage from './pages/registro/RegistroPage';



//const url= process.env.REACT_APP_BACK_URL;




function App() {


  return (
    <div className="App">

            <Routes>
              <Route exact path="/" element={<JuegoPage />} />
              <Route exact path="/juegos" element={<JuegoPage />} />
              <Route exact path="/juegos/:id" element={<JuegoDetalles />} />
              <Route exact path="/register" element={<RegistroPage />} />
              <Route exact path="/login" element={<LoginPage />} />
            </Routes>
      <footer className='App'>
        <Footer/>
      </footer>
    </div>
  );
}

export default App;
