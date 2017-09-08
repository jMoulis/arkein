/**
 * Created by julien on 07/09/17.
 */

/*
 * Npm import
 */
import React from 'react';
import axios from 'axios';

const interviews = [
    {
        id: 2,
        date: '12/02/1234',
        objet: 'Je teste sans axios',
        odr: 'Action sans action',
        organisateur: 'Julien',
        jeune: 'Simon'
    },
    {
        id: 3,
        date: '12/02/1235',
        objet: 'Je teste axios',
        odr: 'Action son',
        organisateur: 'Raymond',
        jeune: 'julien'
    }
];

/*
 * Local import
 */
import Table from 'src/interviewBundle/components/Table';

/*
 * Code
 */
class App extends React.Component {
    state = {
        interviews:[]
    };

    componentDidMount = () => {
        this.loadInterviews();
    };

    loadInterviews = () => {
        // Je récupère les données et je les mets dans le state
        const user = 24;
        axios
            .get(Routing.generate('entretien_list_by_author', {id: user}))
            .then(({ data }) => {
                this.setState({
                    interviews: data.items
                })
            })
    };


  render() {
    return(
      <div id="app">
        <Table interviews={this.state.interviews} />
      </div>
    )
  }
}


/*
 * Export default
 */
export default App;