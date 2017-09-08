/**
 * Created by julien on 07/09/17.
 */
/*
 * Npm import
 */
import 'babel-polyfill';
import React from 'react';
import { render } from 'react-dom';


/*
 * Local import
 */
import App from 'src/interviewBundle/components/App';


/*
 * Code
 */
document.addEventListener('DOMContentLoaded', () => {
  render(<App />, document.getElementById('root'));
});