const data = {
    "title": "General Assembly Voting",
    "topic": "Should the company introduce a four-day work week?",
    "votes": [
        {"person": "Alice", "vote": "pro"},
        {"person": "Bob", "vote": "con"},
        {"person": "Charlie", "vote": "pro"},
        {"person": "David", "vote": "abs"},
        {"person": "Eva", "vote": "pro"},
        {"person": "Frank", "vote": "con"},
        {"person": "Grace", "vote": "pro"},
        {"person": "Hannah", "vote": "con"},
        {"person": "Ian", "vote": "pro"},
        {"person": "Julia", "vote": "abs"},
        {"person": "Kevin", "vote": "con"},
        {"person": "Laura", "vote": "pro"},
        {"person": "Michael", "vote": "pro"},
        {"person": "Nina", "vote": "con"},
        {"person": "Oliver", "vote": "pro"},
        {"person": "Paula", "vote": "abs"},
        {"person": "Quentin", "vote": "con"},
        {"person": "Rachel", "vote": "pro"},
        {"person": "Samuel", "vote": "con"},
        {"person": "Tina", "vote": "pro"},
        {"person": "Uma", "vote": "abs"},
        {"person": "Victor", "vote": "con"},
        {"person": "Wendy", "vote": "pro"},
        {"person": "Xavier", "vote": "pro"},
        {"person": "Yasmin", "vote": "con"},
        {"person": "Zach", "vote": "abs"},
        {"person": "Aaron", "vote": "pro"},
        {"person": "Bella", "vote": "con"},
        {"person": "Carlos", "vote": "pro"},
        {"person": "Diana", "vote": "con"},
        {"person": "Ethan", "vote": "pro"},
        {"person": "Fiona", "vote": "abs"},
        {"person": "George", "vote": "pro"},
        {"person": "Helen", "vote": "con"},
        {"person": "Isaac", "vote": "pro"},
        {"person": "Jasmine", "vote": "con"},
        {"person": "Kyle", "vote": "pro"},
        {"person": "Liam", "vote": "abs"},
        {"person": "Maya", "vote": "pro"},
        {"person": "Noah", "vote": "con"},
        {"person": "Olivia", "vote": "pro"},
        {"person": "Peter", "vote": "con"},
        {"person": "Quincy", "vote": "abs"},
        {"person": "Ruby", "vote": "pro"},
        {"person": "Steven", "vote": "con"},
        {"person": "Taylor", "vote": "pro"},
        {"person": "Ursula", "vote": "pro"},
        {"person": "Victor", "vote": "con"},
        {"person": "William", "vote": "abs"},
        {"person": "Zoe", "vote": "pro"},
        {"person": "Adam", "vote": "con"},
        {"person": "Beth", "vote": "pro"},
        {"person": "Chris", "vote": "pro"},
        {"person": "Derek", "vote": "con"},
        {"person": "Emily", "vote": "abs"},
        {"person": "Fred", "vote": "pro"},
        {"person": "Gina", "vote": "con"},
        {"person": "Harry", "vote": "pro"},
        {"person": "Ivy", "vote": "con"},
        {"person": "Jack", "vote": "pro"},
        {"person": "Kate", "vote": "abs"},
        {"person": "Leo", "vote": "con"},
        {"person": "Molly", "vote": "pro"},
        {"person": "Nathan", "vote": "pro"},
        {"person": "Olive", "vote": "con"},
        {"person": "Patrick", "vote": "abs"},
        {"person": "Rita", "vote": "pro"},
        {"person": "Scott", "vote": "con"},
        {"person": "Teresa", "vote": "pro"},
        {"person": "Ulysses", "vote": "con"},
        {"person": "Valerie", "vote": "pro"},
        {"person": "Walter", "vote": "abs"},
        {"person": "Yvonne", "vote": "pro"},
        {"person": "Zane", "vote": "con"},
        {"person": "Amber", "vote": "pro"},
        {"person": "Brandon", "vote": "con"},
        {"person": "Clara", "vote": "pro"},
        {"person": "Damian", "vote": "abs"},
        {"person": "Elena", "vote": "pro"},
        {"person": "Felix", "vote": "con"},
        {"person": "Gabriela", "vote": "pro"},
        {"person": "Hugo", "vote": "con"},
        {"person": "Isabel", "vote": "pro"},
        {"person": "Jonas", "vote": "abs"},
        {"person": "Kara", "vote": "con"},
        {"person": "Lucas", "vote": "pro"},
        {"person": "Marina", "vote": "pro"},
        {"person": "Nicolas", "vote": "con"},
        {"person": "Olga", "vote": "abs"},
        {"person": "Rafael", "vote": "pro"},
        {"person": "Sofia", "vote": "con"},
        {"person": "Theo", "vote": "pro"},
        {"person": "Valentina", "vote": "con"},
        {"person": "Wesley", "vote": "pro"},
        {"person": "Yara", "vote": "abs"},
        {"person": "Arthur", "vote": "pro"},
        {"person": "Bianca", "vote": "con"},
        {"person": "Cameron", "vote": "pro"},
        {"person": "Doris", "vote": "con"},
        {"person": "Edward", "vote": "pro"},
        {"person": "Frances", "vote": "abs"}
    ]
};


const title = document.querySelector('.title');


const description = document.querySelector('.description');

title.innerHTML = data.title;

description.innerHTML = data.topic;


const list = document.getElementById('voteList');


const proCard = document.querySelector('.pro-card');
const conCard = document.querySelector('.con-card');
const absCard = document.querySelector('.abs-card');

/**
 * render the list
 */
function render() {
    list.innerHTML = '';

    let grouped = Object.groupBy(data.votes, (item) => item.vote)

    proCard.querySelector('.count').innerHTML = grouped.pro.length;
    conCard.querySelector('.count').innerHTML = grouped.con.length;
    absCard.querySelector('.count').innerHTML = grouped.abs.length;

    let best = 'pro';


    if (grouped.con > grouped.pro) {
        best = "con";
    }

    if (grouped.abs > grouped.con) {
        best = "abs";
    }


    proCard.querySelector('.percent').innerHTML = ((grouped.pro.length / data.votes.length) * 100).toFixed(2) + "%";
    conCard.querySelector('.percent').innerHTML = ((grouped.con.length / data.votes.length) * 100).toFixed(2) + "%";
    absCard.querySelector('.percent').innerHTML = ((grouped.abs.length / data.votes.length) * 100).toFixed(2) + "%";
    proCard.classList.remove("high");
    conCard.classList.remove("high");
    absCard.classList.remove("high");
    document.querySelector(`.${best}-card`).classList.add("high")


    data.votes.forEach(vote => {

        list.insertAdjacentHTML('beforeend', `
        <article class="card">
                <h3>${vote.person}</h3>
                <span class="${vote.vote}">${vote.vote}</span>
</article>
        `)
    });
}

render();

