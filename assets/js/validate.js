const validation = new JustValidate("#login");

validation
    .addField("#log_email", [
        {
            rule: "required"
        },
        {
            rule: "email"
        }
    ]);