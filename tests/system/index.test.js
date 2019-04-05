const chai = require('chai'),
      chaiHttp = require('chai-http'),
      expect = require('chai').expect,
      api = 'http://melbourne.sma.com.au/stagearea/api/';

chai.use(chaiHttp);

describe('System Tests', () => {
    describe('API Failures', () => {
        it('It should fail to find an endpoint', () => {
            chai.request(api).get('notfound').end(function(err, res) {
                expect(res).to.have.status(404);
                expect(res.body).to.be.a('object');
            })
        });
    });

    describe('Auth Tests', () => {
        describe('Failures', () => {
            it('It should fail to login', () => {
                chai.request(api).post('auth').send({
                    "auth": {
                        "email": "pete",
                        "password": "secret"
                    }
                }).end(function(err, res) {
                    expect(res).to.have.status(400);
                    expect(res.body).to.be.a('object');
                })
            });

        })
    });
});