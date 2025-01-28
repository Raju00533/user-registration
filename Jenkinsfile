pipeline {
    agent any
    environment {
        SERVER_IP = credentials('QA_AUTOMATION_SERVER_IP')
        SERVER_USERNAME = credentials('QA_AUTOMATION_SERVER_USERNAME')
        SERVER_PASSWORD = credentials('QA_AUTOMATION_SERVER_PASS')
        TEAMS_CHANNEL_EMAIL = credentials('TEAMS_CHANNEL_EMAIL')
    }
    stages {
        stage('Setup GitHub Credentials') {
            steps {
                script {
                    withEnv(["MY_EMAIL=${env.TG_GITHUB_EMAIL}", "MY_USERNAME=${env.TG_GITHUB_USERNAME}"]) {
                        sh '''
                            git config --global user.email "$MY_EMAIL"
                            git config --global user.name "$MY_USERNAME"
                        '''
                    }
                }
            }
        }
 
        stage('Checkout Code') {
            steps {
                checkout scm
            }
        }
        stage('Setup Environment') {
            steps {
                script {
                    sh '''
                        mkdir -p ~/.ssh
                        echo "${TG_PRIVATE_SSH_KEY}" > ~/.ssh/id_rsa
                        chmod 600 ~/.ssh/id_rsa
                    '''
                }
            }
        }
        stage('Install Dependencies and Build') {
            steps {
                script {
                    sh '''
                        nvm install 12
                        php -v
                        composer install
                        npm install --legacy-peer-deps --force
                        npm run build --legacy-peer-deps --force
                        composer install --no-dev
                    '''
                }
            }
        }
        stage('Prepare Plugin ZIP') {
            steps {
                sh '''
                    mkdir -p everest-forms
                    rsync -rc --exclude-from="./.distignore" "./" "./everest-forms" --delete --delete-excluded
                    zip -r plugin.zip everest-forms
                '''
            }
        }
        stage('Upload Plugin and Activate') {
            steps {
                script {
                    sh '''
                        scp -o StrictHostKeyChecking=no plugin.zip ${SERVER_USERNAME}@${SERVER_IP}:/tmp/
                        ssh -o StrictHostKeyChecking=no ${SERVER_USERNAME}@${SERVER_IP} << 'EOF'
                        cd /path/to/wordpress
                        wp plugin install /tmp/plugin.zip --activate
                        rm -f /tmp/plugin.zip
                        EOF
                    '''
                }
            }
        }
        stage('Automation Testing') {
            steps {
                script {
                    sh '''
                        mkdir -p python-code
                        cd python-code
                        git init
                        git remote add origin git@github.com:wpeverest/EVF-Automation.git
                        git pull origin test-evf-free
                        pip install -r requirements.txt
                        chmod +x test_evf_free.sh
                        bash ./test_evf_free.sh
                    '''
                }
            }
        }
        stage('Generate and Upload Reports') {
            steps {
                script {
                    sh '''
                        mkdir -p /home/master/applications/ycrdmckpsu/public_html/evftest/reports/
                        rm -rf /home/master/applications/ycrdmckpsu/public_html/evftest/reports/*
                        scp -o StrictHostKeyChecking=no -r python-code/results/* ${SERVER_USERNAME}@${SERVER_IP}:/home/master/applications/ycrdmckpsu/public_html/evftest/reports/
                    '''
                }
            }
        }
    }
    post {
        success {
            emailext(
                subject: "EVF QA Automation - Job Successful",
                body: """
                <p>The EVF QA Automation job completed successfully.</p>
                <p>Here are the details:</p>
                <ul>
                    <li><b>Test Report:</b> <a href="https://qatest.wptests.net/evftest/reports/report.html">View Report</a></li>
                    <li><b>Plugin ZIP:</b> <a href="https://qatest.wptests.net/evftest/reports/plugin.zip">Download Plugin</a></li>
                </ul>
                """,
                to: "${TEAMS_CHANNEL_EMAIL}",
                mimeType: 'text/html'
            )
        }
        failure {
            emailext(
                subject: "EVF QA Automation - Job Failed",
                body: """
                <p>The EVF QA Automation job failed.</p>
                <p>Please check the Jenkins logs for more details.</p>
                """,
                to: "${TEAMS_CHANNEL_EMAIL}",
                mimeType: 'text/html'
            )
        }
        always {
            cleanWs()
        }
    }
}
